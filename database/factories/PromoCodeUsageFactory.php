<?php

namespace Database\Factories;

use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromoCodeUsage>
 */
class PromoCodeUsageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'promo_code_id' => PromoCode::factory(),
            'user_id' => User::factory(),
            'used_at' => now(),
        ];
    }
}
