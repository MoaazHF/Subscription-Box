<?php

namespace Database\Factories;

use App\Models\DeliveryZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryZone>
 */
class DeliveryZoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->city().' Zone',
            'region' => fake()->state(),
            'country' => 'EG',
            'is_serviceable' => true,
        ];
    }
}
