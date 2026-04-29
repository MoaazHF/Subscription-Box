<?php

namespace Database\Factories;

use App\Models\Box;
use App\Models\SocialPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialPost>
 */
class SocialPostFactory extends Factory
{
    public function definition(): array
    {
        $box = Box::factory()->create();

        return [
            'user_id' => User::factory(),
            'box_id' => $box->id,
            'caption' => fake()->sentence(),
            'photo_url' => fake()->optional()->imageUrl(),
            'visibility' => fake()->randomElement(['public', 'private']),
            'loyalty_points_awarded' => 5,
            'is_deleted' => false,
            'created_at' => now(),
        ];
    }
}
