<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\InventoryLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<InventoryLog>
 */
class InventoryLogFactory extends Factory
{
    protected $model = InventoryLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'delivery_id' => Delivery::factory(),
            'event' => 'status_changed',
            'from_value' => 'pending',
            'to_value' => 'picking',
            'changed_by' => null,
            'changed_by_type' => 'user',
            'notes' => null,
        ];
    }
}
