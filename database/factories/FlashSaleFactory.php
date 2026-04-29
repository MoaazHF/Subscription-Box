<?php

namespace Database\Factories;

use App\Models\FlashSale;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FlashSale>
 */
class FlashSaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'plan_id' => SubscriptionPlan::query()->value('id') ?? 1,
            'created_by' => User::factory()->create([
                'role_id' => Role::query()->firstOrCreate(['name' => Role::ADMIN])->id,
            ])->id,
            'name' => fake()->sentence(3),
            'discount_percent' => fake()->numberBetween(5, 50),
            'stock_limit' => 100,
            'claimed_count' => 0,
            'start_at' => now()->subHour(),
            'end_at' => now()->addDay(),
            'created_at' => now(),
        ];
    }
}
