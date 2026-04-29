<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRetentionOfferRequest;
use App\Http\Requests\UpdateRetentionOfferRequest;
use App\Models\RetentionOffer;
use App\Models\Subscription;
use App\Services\RetentionOfferService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RetentionOfferController extends Controller
{
    public function __construct(private RetentionOfferService $retentionOfferService) {}

    public function index(Request $request): View
    {
        $offers = RetentionOffer::query()
            ->whereHas('subscription', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest('presented_at')
            ->get();

        return view('retention-offers.index', [
            'offers' => $offers,
        ]);
    }

    public function store(StoreRetentionOfferRequest $request, Subscription $subscription): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $subscription->user_id === $request->user()->id, 403);

        $this->retentionOfferService->present($subscription, $request->validated());

        return back()->with('status', 'Retention offer presented.');
    }

    public function update(UpdateRetentionOfferRequest $request, RetentionOffer $retentionOffer): RedirectResponse
    {
        $ownerId = $retentionOffer->subscription()->value('user_id');
        abort_unless($request->user()->isAdmin() || $ownerId === $request->user()->id, 403);

        $this->retentionOfferService->updateDecision($retentionOffer, (bool) $request->validated('accepted'));

        return back()->with('status', 'Retention offer updated.');
    }
}
