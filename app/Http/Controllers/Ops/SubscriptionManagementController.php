<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreManagedSubscriptionRequest;
use App\Http\Requests\UpdateManagedSubscriptionRequest;
use App\Models\Address;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService,
        private AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        return view('ops.subscriptions.index', [
            'subscriptions' => Subscription::query()->with(['user', 'plan', 'address', 'payments'])->latest()->get(),
            'subscribers' => User::query()
                ->whereHas('role', fn ($query) => $query->where('name', 'subscriber'))
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'plans' => SubscriptionPlan::query()->where('is_active', true)->orderBy('price_monthly')->get(),
            'addresses' => Address::query()->with('user:id,name,email')->latest()->get(),
        ]);
    }

    public function store(StoreManagedSubscriptionRequest $request): RedirectResponse
    {
        $subscriber = User::query()->findOrFail($request->validated('user_id'));

        if (! $subscriber->isSubscriber()) {
            return back()->with('error', 'Selected user must have subscriber role.');
        }

        $address = Address::query()->findOrFail($request->validated('address_id'));

        if ($address->user_id !== $subscriber->id) {
            return back()->with('error', 'Selected address does not belong to the selected subscriber.');
        }

        $plan = SubscriptionPlan::query()
            ->where('is_active', true)
            ->findOrFail($request->validated('plan_id'));

        $subscription = $this->subscriptionService->createForUser($subscriber, $plan, [
            ...$request->validated(),
            'payment_gateway_status' => 'success',
            'payment_gateway_ref' => 'ADMIN-'.strtoupper(substr((string) str()->uuid(), 0, 8)),
            'payment_card_last4' => '0000',
            'payment_gateway_reason' => 'admin_created',
        ], $request->ip());

        $this->auditLogService->record($request->user(), 'subscription.admin_created', $subscription, [
            'subscriber_id' => $subscriber->id,
            'plan_id' => $plan->id,
        ], $request->ip());

        return back()->with('status', 'Subscription created from admin panel.');
    }

    public function update(UpdateManagedSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $address = Address::query()->findOrFail($request->validated('address_id'));

        if ($address->user_id !== $subscription->user_id) {
            return back()->with('error', 'Address must belong to the subscription owner.');
        }

        $subscription->update([
            'plan_id' => $request->validated('plan_id'),
            'address_id' => $request->validated('address_id'),
            'status' => $request->validated('status'),
            'auto_renew' => (bool) ($request->validated('auto_renew') ?? false),
            'eco_shipping' => (bool) ($request->validated('eco_shipping') ?? false),
        ]);

        $this->auditLogService->record($request->user(), 'subscription.admin_updated', $subscription, $request->validated(), $request->ip());

        return back()->with('status', 'Subscription updated.');
    }

    public function destroy(Request $request, Subscription $subscription): RedirectResponse
    {
        $snapshot = $subscription->load(['user', 'plan'])->toArray();

        $subscription->delete();

        $this->auditLogService->record($request->user(), 'subscription.admin_deleted', $subscription, $snapshot, $request->ip());

        return back()->with('status', 'Subscription deleted.');
    }
}
