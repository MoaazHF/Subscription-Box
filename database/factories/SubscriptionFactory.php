<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        return [
            'user_id' => $user->id,
            'plan_id' => SubscriptionPlan::query()->value('id') ?? 1,
            'address_id' => $address->id,
            'status' => 'active',
            'start_date' => now()->toDateString(),
            'next_billing_date' => now()->addMonth()->toDateString(),
            'remaining_billing_days' => 30,
            'auto_renew' => true,
            'eco_shipping' => false,
            'loyalty_points' => 0,
        ];
    }
}
