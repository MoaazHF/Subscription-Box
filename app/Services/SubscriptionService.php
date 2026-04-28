<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(
        private BillingService $billingService,
        private BoxProvisioningService $boxProvisioningService,
        private AuditLogService $auditLogService
    ) {}

    /**
     * @param  array{address_id:string,start_date:string,auto_renew?:bool,eco_shipping?:bool}  $payload
     */
    public function createForUser(User $user, SubscriptionPlan $plan, array $payload, ?string $ipAddress = null): Subscription
    {
        $startDate = Carbon::parse($payload['start_date'])->startOfDay();
        $address = Address::query()
            ->whereBelongsTo($user)
            ->findOrFail($payload['address_id']);

        return DB::transaction(function () use ($address, $ipAddress, $payload, $plan, $startDate, $user): Subscription {
            $subscription = $user->subscriptions()->create([
                'plan_id' => $plan->id,
                'address_id' => $address->id,
                'status' => 'active',
                'start_date' => $startDate->toDateString(),
                'next_billing_date' => $startDate->copy()->addDays(config('subscriptions.billing_cycle_days', 30))->toDateString(),
                'remaining_billing_days' => config('subscriptions.billing_cycle_days', 30),
                'auto_renew' => (bool) ($payload['auto_renew'] ?? true),
                'eco_shipping' => (bool) ($payload['eco_shipping'] ?? false),
                'loyalty_points' => 0,
            ]);

            $this->boxProvisioningService->provisionCurrentBox($subscription);

            $payment = $this->billingService->charge($subscription, 'subscription_started');

            $this->auditLogService->record($user, 'subscription.created', $subscription, [
                'plan' => $plan->name,
                'payment_id' => $payment->id,
            ], $ipAddress);

            return $subscription->load(['plan', 'address', 'payments']);
        });
    }

    public function pause(Subscription $subscription, User $user, ?string $ipAddress = null): void
    {
        if ($subscription->status !== 'active') {
            return;
        }

        $nextBillingDate = $subscription->next_billing_date ? Carbon::parse($subscription->next_billing_date) : now();

        $subscription->update([
            'status' => 'paused',
            'remaining_billing_days' => max(now()->diffInDays($nextBillingDate, false), 0),
        ]);

        $this->auditLogService->record($user, 'subscription.paused', $subscription, [], $ipAddress);
    }

    public function resume(Subscription $subscription, User $user, ?string $ipAddress = null): void
    {
        if ($subscription->status !== 'paused') {
            return;
        }

        $daysToRestore = max($subscription->remaining_billing_days, 1);

        $subscription->update([
            'status' => 'active',
            'next_billing_date' => now()->addDays($daysToRestore)->toDateString(),
        ]);

        $this->auditLogService->record($user, 'subscription.resumed', $subscription, [], $ipAddress);
    }

    public function changePlan(
        Subscription $subscription,
        SubscriptionPlan $newPlan,
        User $user,
        ?string $ipAddress = null
    ): void {
        $oldPlanName = $subscription->plan?->name;

        DB::transaction(function () use ($ipAddress, $newPlan, $oldPlanName, $subscription, $user): void {
            $subscription->update([
                'plan_id' => $newPlan->id,
            ]);

            $payment = $this->billingService->charge($subscription->fresh(['plan']), 'plan_changed');

            $this->auditLogService->record($user, 'subscription.plan_changed', $subscription, [
                'from' => $oldPlanName,
                'to' => $newPlan->name,
                'payment_id' => $payment->id,
            ], $ipAddress);
        });
    }

    public function renewDueSubscriptions(): int
    {
        $subscriptions = Subscription::query()
            ->with(['plan', 'user'])
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->whereDate('next_billing_date', '<=', now()->toDateString())
            ->get();

        foreach ($subscriptions as $subscription) {
            DB::transaction(function () use ($subscription): void {
                $payment = $this->billingService->charge($subscription, 'subscription_renewed');

                $subscription->update([
                    'next_billing_date' => Carbon::parse($subscription->next_billing_date)
                        ->addDays(config('subscriptions.billing_cycle_days', 30))
                        ->toDateString(),
                    'remaining_billing_days' => config('subscriptions.billing_cycle_days', 30),
                    'loyalty_points' => $subscription->loyalty_points + 10,
                ]);

                $this->auditLogService->record($subscription->user, 'subscription.renewed', $subscription, [
                    'payment_id' => $payment->id,
                ]);
            });
        }

        return $subscriptions->count();
    }
}
