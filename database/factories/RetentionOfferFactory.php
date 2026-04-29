<?php

namespace Database\Factories;

use App\Models\RetentionOffer;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RetentionOffer>
 */
class RetentionOfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'offer_type' => fake()->randomElement(['discount', 'frequency_change', 'plan_downgrade']),
            'offer_value' => '10%',
            'cancellation_reason' => fake()->optional()->sentence(),
            'presented_at' => now(),
            'accepted' => false,
            'accepted_at' => null,
        ];
    }
}
