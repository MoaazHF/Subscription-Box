<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralService
{
    public function __construct(private RewardService $rewardService) {}

    public function create(User $referrer, User $referee): Referral
    {
        abort_if($referrer->id === $referee->id, 422, 'You cannot refer yourself.');

        return Referral::query()->firstOrCreate(
            [
                'referrer_id' => $referrer->id,
                'referee_id' => $referee->id,
            ],
            [
                'referral_code' => Str::upper(Str::random(10)),
                'status' => 'pending',
                'reward_applied' => false,
            ]
        );
    }

    public function confirm(Referral $referral): Referral
    {
        if ($referral->status === 'confirmed') {
            return $referral;
        }

        DB::transaction(function () use ($referral): void {
            $referral->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            if (! $referral->reward_applied) {
                $this->rewardService->issue($referral->referrer, [
                    'type' => 'loyalty_points',
                    'points' => 50,
                    'description' => 'Referral confirmed reward',
                ]);

                $referral->update([
                    'reward_applied' => true,
                ]);
            }
        });

        return $referral->fresh();
    }

    public function reject(Referral $referral): Referral
    {
        $referral->update([
            'status' => 'rejected',
        ]);

        return $referral->fresh();
    }
}
