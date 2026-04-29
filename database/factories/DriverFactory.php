<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create([
                'role_id' => Role::query()->firstOrCreate(['name' => Role::DRIVER])->id,
            ])->id,
            'vehicle_number' => strtoupper(fake()->bothify('??-####')),
            'is_active' => true,
        ];
    }
}
