<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\DeliveryZone;
use App\Models\Driver;
use App\Models\User;
use App\Models\WarehouseStaff;

class OperationsManagementService
{
    /** @param array{user_id:string,vehicle_number?:string,is_active?:bool} $payload */
    public function upsertDriver(array $payload): Driver
    {
        $driver = Driver::query()->updateOrCreate(
            ['user_id' => $payload['user_id']],
            [
                'vehicle_number' => $payload['vehicle_number'] ?? null,
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ]
        );

        return $driver->fresh();
    }

    public function assignDelivery(Driver $driver, Delivery $delivery): Delivery
    {
        if (! $driver->is_active) {
            abort(422, 'Selected driver is not active.');
        }

        if (in_array($delivery->status, [Delivery::DELIVERED, Delivery::UNDELIVERABLE], true)) {
            abort(422, 'Only active deliveries can be assigned to a driver.');
        }

        $delivery->update([
            'driver_id' => $driver->id,
        ]);

        return $delivery->fresh(['driver', 'address', 'box.subscription.user']);
    }

    /** @param array{user_id:string,warehouse_location?:string} $payload */
    public function upsertWarehouseStaff(array $payload): WarehouseStaff
    {
        $profile = WarehouseStaff::query()->updateOrCreate(
            ['user_id' => $payload['user_id']],
            ['warehouse_location' => $payload['warehouse_location'] ?? null]
        );

        return $profile->fresh();
    }

    /** @param array{name:string,region?:string,country:string,is_serviceable?:bool} $payload */
    public function createDeliveryZone(array $payload): DeliveryZone
    {
        return DeliveryZone::create([
            'name' => $payload['name'],
            'region' => $payload['region'] ?? null,
            'country' => strtoupper($payload['country']),
            'is_serviceable' => (bool) ($payload['is_serviceable'] ?? true),
        ]);
    }

    public function toggleZoneServiceability(DeliveryZone $zone): DeliveryZone
    {
        $zone->update([
            'is_serviceable' => ! $zone->is_serviceable,
        ]);

        return $zone->fresh();
    }

    public function assertUserRole(User $user, string $expectedRole): void
    {
        if ($user->role?->name !== $expectedRole) {
            abort(422, "Selected user must have {$expectedRole} role.");
        }
    }
}
