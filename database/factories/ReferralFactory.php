<?php

namespace Database\Factories;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Referral>
 */
class ReferralFactory extends Factory
{
    public function definition(): array
    {
        return [
            'referrer_id' => User::factory(),
            'referee_id' => User::factory(),
            'referral_code' => Str::upper(Str::random(10)),
            'status' => 'pending',
            'reward_applied' => false,
            'confirmed_at' => null,
        ];
    }
}
