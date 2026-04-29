<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\User;
use App\Services\RewardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function __construct(private RewardService $rewardService) {}

    public function index(Request $request): View
    {
        return view('rewards.index', [
            'rewards' => Reward::query()->where('user_id', $request->user()->id)->latest('created_at')->get(),
        ]);
    }

    public function issue(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'type' => ['required', 'string', 'in:account_credit,free_box,loyalty_points,anniversary_item'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'points' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::query()->findOrFail($validated['user_id']);
        $this->rewardService->issue($user, $validated);

        return back()->with('status', 'Reward issued.');
    }

    public function apply(Request $request, Reward $reward): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $reward->user_id === $request->user()->id, 403);

        $this->rewardService->apply($reward);

        return back()->with('status', 'Reward applied.');
    }
}
