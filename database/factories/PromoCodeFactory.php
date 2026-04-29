<?php

namespace Database\Factories;

use App\Models\PromoCode;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PromoCode>
 */
class PromoCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by' => User::factory()->create([
                'role_id' => Role::query()->firstOrCreate(['name' => Role::ADMIN])->id,
            ])->id,
            'code' => 'CODE'.Str::upper(Str::random(6)),
            'discount_type' => fake()->randomElement(['percent', 'fixed']),
            'discount_value' => fake()->randomFloat(2, 5, 40),
            'max_uses' => 100,
            'used_count' => 0,
            'expires_at' => now()->addDays(10),
            'created_at' => now(),
        ];
    }
}
