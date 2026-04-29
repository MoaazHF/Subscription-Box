<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\WarehouseStaff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WarehouseStaff>
 */
class WarehouseStaffFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create([
                'role_id' => Role::query()->firstOrCreate(['name' => Role::WAREHOUSE_STAFF])->id,
            ])->id,
            'warehouse_location' => fake()->city(),
        ];
    }
}
