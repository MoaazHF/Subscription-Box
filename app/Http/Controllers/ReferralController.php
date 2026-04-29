<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReferralRequest;
use App\Models\Referral;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function __construct(private ReferralService $referralService) {}

    public function index(Request $request): View
    {
        $referrals = Referral::query()
            ->where('referrer_id', $request->user()->id)
            ->orWhere('referee_id', $request->user()->id)
            ->latest()
            ->get();

        return view('referrals.index', [
            'referrals' => $referrals,
        ]);
    }

    public function store(StoreReferralRequest $request): RedirectResponse
    {
        $referee = User::query()->findOrFail($request->validated('referee_id'));

        $this->referralService->create($request->user(), $referee);

        return back()->with('status', 'Referral created.');
    }

    public function confirm(Request $request, Referral $referral): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $referral->referee_id === $request->user()->id, 403);

        $this->referralService->confirm($referral);

        return back()->with('status', 'Referral confirmed.');
    }

    public function reject(Request $request, Referral $referral): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $referral->referee_id === $request->user()->id, 403);

        $this->referralService->reject($referral);

        return back()->with('status', 'Referral rejected.');
    }
}
