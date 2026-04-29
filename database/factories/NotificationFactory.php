<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['email', 'sms', 'push']),
            'event_type' => 'delivery_update',
            'subject' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'status' => fake()->randomElement(['queued', 'sent', 'failed']),
            'sent_at' => null,
        ];
    }
}
