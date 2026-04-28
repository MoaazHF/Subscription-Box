<?php

namespace Database\Factories;

use App\Models\Claim;
use App\Models\Delivery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Claim>
 */
class ClaimFactory extends Factory
{
    protected $model = Claim::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'delivery_id' => Delivery::factory(),
            'type' => fake()->randomElement(['damaged', 'missing']),
            'description' => fake()->paragraph(3),
            'photo_path' => null,
            'status' => 'open',
        ];
    }

    /**
     * Indicate the claim is for a damaged item.
     */
    public function damaged(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'damaged']);
    }

    /**
     * Indicate the claim is for a missing box.
     */
    public function missing(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'missing']);
    }

    /**
     * Indicate the claim has been resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'resolved']);
    }

    /**
     * Indicate the claim is under review.
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'under_review']);
    }
}
