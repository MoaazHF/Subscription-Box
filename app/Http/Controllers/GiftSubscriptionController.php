<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivateGiftSubscriptionRequest;
use App\Http\Requests\PurchaseGiftSubscriptionRequest;
use App\Models\GiftSubscription;
use App\Services\GiftSubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GiftSubscriptionController extends Controller
{
    public function __construct(private GiftSubscriptionService $giftSubscriptionService) {}

    public function index(Request $request): View
    {
        return view('gift-subscriptions.index', [
            'gifts' => GiftSubscription::query()
                ->where('purchaser_id', $request->user()->id)
                ->orWhere('recipient_user_id', $request->user()->id)
                ->latest('purchased_at')
                ->get(),
        ]);
    }

    public function purchase(PurchaseGiftSubscriptionRequest $request): RedirectResponse
    {
        $this->giftSubscriptionService->purchase($request->user(), $request->validated());

        return back()->with('status', 'Gift subscription purchased.');
    }

    public function activate(ActivateGiftSubscriptionRequest $request): RedirectResponse
    {
        $activationCode = strtoupper($request->validated('activation_code'));

        $giftSubscription = GiftSubscription::query()
            ->where('activation_code', $activationCode)
            ->firstOrFail();

        $this->giftSubscriptionService->activate(
            $giftSubscription,
            $request->user(),
            $request->validated('address_id')
        );

        return back()->with('status', 'Gift subscription activated.');
    }
}
