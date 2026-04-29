<?php

namespace App\Services;

use App\Models\Reward;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RewardService
{
    /** @param array{type:string,amount?:float,points?:int,description?:string} $payload */
    public function issue(User $user, array $payload): Reward
    {
        return Reward::create([
            'user_id' => $user->id,
            'type' => $payload['type'],
            'amount' => $payload['amount'] ?? null,
            'points' => $payload['points'] ?? null,
            'description' => $payload['description'] ?? null,
            'is_applied' => false,
            'created_at' => now(),
        ]);
    }

    public function apply(Reward $reward): Reward
    {
        if ($reward->is_applied) {
            return $reward;
        }

        DB::transaction(function () use ($reward): void {
            $reward->loadMissing('user.subscriptions');

            if ($reward->points) {
                $subscription = $reward->user->subscriptions()
                    ->orderByDesc('created_at')
                    ->first();

                if ($subscription) {
                    $subscription->increment('loyalty_points', $reward->points);
                }
            }

            $reward->update([
                'is_applied' => true,
                'applied_at' => now(),
            ]);
        });

        return $reward->fresh();
    }
}
