<?php

namespace Database\Factories;

use App\Models\Box;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Box>
 */
class BoxFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'period_month' => now()->month,
            'period_year' => now()->year,
            'status' => 'open',
            'lock_date' => now()->addDays(5)->toDateString(),
            'theme' => 'Factory Box',
            'total_weight_g' => 0,
            'shipping_tier' => 'standard',
        ];
    }
}
