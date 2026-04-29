<?php

namespace Database\Factories;

use App\Models\Reward;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reward>
 */
class RewardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['account_credit', 'free_box', 'loyalty_points', 'anniversary_item']),
            'amount' => fake()->randomFloat(2, 1, 30),
            'points' => fake()->numberBetween(10, 100),
            'description' => fake()->sentence(),
            'is_applied' => false,
            'created_at' => now(),
            'applied_at' => null,
        ];
    }
}
