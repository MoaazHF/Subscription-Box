<?php

namespace Database\Factories;

use App\Models\GiftSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GiftSubscription>
 */
class GiftSubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'purchaser_id' => User::factory(),
            'recipient_user_id' => null,
            'plan_id' => SubscriptionPlan::query()->value('id') ?? 1,
            'subscription_id' => null,
            'recipient_email' => fake()->safeEmail(),
            'recipient_name' => fake()->name(),
            'duration_months' => fake()->numberBetween(1, 12),
            'activation_code' => Str::upper(Str::random(32)),
            'status' => 'pending_activation',
            'personal_message' => fake()->optional()->sentence(),
            'purchased_at' => now(),
            'activated_at' => null,
            'scheduled_send_at' => null,
            'expires_at' => now()->addMonths(6),
        ];
    }
}
