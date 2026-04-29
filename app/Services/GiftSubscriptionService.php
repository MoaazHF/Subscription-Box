<?php

namespace App\Services;

use App\Models\GiftSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GiftSubscriptionService
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    /** @param array{plan_id:int,recipient_email:string,recipient_name?:string,duration_months:int,personal_message?:string,scheduled_send_at?:string} $payload */
    public function purchase(User $purchaser, array $payload): GiftSubscription
    {
        $plan = SubscriptionPlan::query()->findOrFail($payload['plan_id']);

        return GiftSubscription::create([
            'purchaser_id' => $purchaser->id,
            'recipient_user_id' => null,
            'plan_id' => $plan->id,
            'subscription_id' => null,
            'recipient_email' => $payload['recipient_email'],
            'recipient_name' => $payload['recipient_name'] ?? null,
            'duration_months' => $payload['duration_months'],
            'activation_code' => Str::upper(Str::random(32)),
            'status' => 'pending_activation',
            'personal_message' => $payload['personal_message'] ?? null,
            'purchased_at' => now(),
            'scheduled_send_at' => $payload['scheduled_send_at'] ?? null,
            'expires_at' => now()->addMonths($payload['duration_months']),
        ]);
    }

    public function activate(GiftSubscription $giftSubscription, User $recipient, string $addressId): GiftSubscription
    {
        abort_if($giftSubscription->status !== 'pending_activation', 422, 'Gift is not activatable.');

        DB::transaction(function () use ($addressId, $giftSubscription, $recipient): void {
            $subscription = $this->subscriptionService->createForUser(
                $recipient,
                $giftSubscription->plan,
                [
                    'address_id' => $addressId,
                    'start_date' => now()->toDateString(),
                    'auto_renew' => false,
                    'eco_shipping' => false,
                ]
            );

            $giftSubscription->update([
                'recipient_user_id' => $recipient->id,
                'subscription_id' => $subscription->id,
                'status' => 'active',
                'activated_at' => now(),
            ]);
        });

        return $giftSubscription->fresh();
    }
}
