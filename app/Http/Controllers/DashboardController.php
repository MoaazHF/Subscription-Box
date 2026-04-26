<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load([
            'role',
            'addresses',
            'subscriptions.plan',
            'subscriptions.address',
        ]);

        $currentSubscription = $user->subscriptions
            ->sortByDesc('created_at')
            ->first();

        $recentPayments = Payment::query()
            ->whereHas('subscription', function ($query) use ($user): void {
                $query->whereBelongsTo($user);
            })
            ->with('subscription.plan')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'currentSubscription' => $currentSubscription,
            'recentPayments' => $recentPayments,
        ]);
    }
}
