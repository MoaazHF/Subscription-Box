<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionPlanRequest;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    public function index(Request $request): View
    {
        return view('subscriptions.index', [
            'subscriptions' => $request->user()->subscriptions()->with(['plan', 'address', 'payments'])->latest()->get(),
            'plans' => SubscriptionPlan::query()->where('is_active', true)->orderBy('price_monthly')->get(),
            'addresses' => $request->user()->addresses()->latest()->get(),
        ]);
    }

    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        $existingSubscription = $request->user()->subscriptions()
            ->whereIn('status', ['active', 'paused'])
            ->exists();

        if ($existingSubscription) {
            return redirect()->route('subscriptions.index')
                ->with('error', 'You already have a subscription to manage.')
                ->with('failure_popup', [
                    'title' => 'Action blocked',
                    'message' => 'You already have an active or paused subscription.',
                    'details' => 'Manage the current subscription before starting a new one.',
                ]);
        }

        $plan = SubscriptionPlan::query()
            ->where('is_active', true)
            ->findOrFail($request->validated('plan_id'));

        $subscription = $this->subscriptionService->createForUser(
            $request->user(),
            $plan,
            $request->validated(),
            $request->ip()
        );

        $latestPayment = $subscription->payments->sortByDesc('created_at')->first();

        if ($latestPayment && $latestPayment->status === 'failed') {
            return redirect()->route('subscriptions.index')
                ->with('error', 'Payment declined. Transaction was saved and subscription is suspended.')
                ->with('failure_popup', [
                    'title' => 'Payment failed',
                    'message' => 'Transaction was declined.',
                    'details' => 'Amount: $'.number_format((float) $latestPayment->amount, 2).' · Ref: '.$latestPayment->gateway_ref,
                ]);
        }

        return redirect()->route('subscriptions.index')
            ->with('status', 'Subscription started.')
            ->with('payment_success', [
                'amount' => number_format((float) $latestPayment?->amount, 2),
                'reference' => $latestPayment?->gateway_ref,
            ]);
    }

    public function pause(Request $request, Subscription $subscription): RedirectResponse
    {
        $this->ensureOwnership($request, $subscription);

        $this->subscriptionService->pause($subscription, $request->user(), $request->ip());

        return back()->with('status', 'Subscription paused.');
    }

    public function resume(Request $request, Subscription $subscription): RedirectResponse
    {
        $this->ensureOwnership($request, $subscription);

        $this->subscriptionService->resume($subscription, $request->user(), $request->ip());

        return back()->with('status', 'Subscription resumed.');
    }

    public function changePlan(
        UpdateSubscriptionPlanRequest $request,
        Subscription $subscription
    ): RedirectResponse {
        $this->ensureOwnership($request, $subscription);

        $newPlan = SubscriptionPlan::query()
            ->where('is_active', true)
            ->findOrFail($request->validated('plan_id'));

        $this->subscriptionService->changePlan($subscription, $newPlan, $request->user(), $request->ip());

        return back()->with('status', 'Plan updated.');
    }

    private function ensureOwnership(Request $request, Subscription $subscription): void
    {
        if ($request->user()->isAdmin()) {
            return;
        }

        abort_unless($subscription->user_id === $request->user()->id, 403);
    }
}
