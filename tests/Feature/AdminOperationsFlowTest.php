<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Delivery;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminOperationsFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_manage_driver_warehouse_staff_and_delivery_zones(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $driverUser = User::factory()->create([
            'role_id' => Role::query()->where('name', Role::DRIVER)->value('id'),
            'email' => 'driver-ops@example.com',
        ]);

        $warehouseUser = User::factory()->create([
            'role_id' => Role::query()->where('name', Role::WAREHOUSE_STAFF)->value('id'),
            'email' => 'warehouse-ops@example.com',
        ]);

        $this->actingAs($admin)->post(route('drivers.store'), [
            'user_id' => $driverUser->id,
            'vehicle_number' => 'OPS-101',
            'is_active' => true,
        ])->assertSessionHas('status');

        $driverId = (string) \DB::table('drivers')->where('user_id', $driverUser->id)->value('id');

        $this->actingAs($admin)->patch(route('drivers.toggle', $driverId))->assertSessionHas('status');

        $this->actingAs($admin)->post(route('warehouse-staff.store'), [
            'user_id' => $warehouseUser->id,
            'warehouse_location' => 'Main Hub',
        ])->assertSessionHas('status');

        $this->actingAs($admin)->post(route('delivery-zones.store'), [
            'name' => 'North Zone',
            'region' => 'North',
            'country' => 'EG',
            'is_serviceable' => true,
        ])->assertSessionHas('status');

        $zoneId = (int) \DB::table('delivery_zones')->where('name', 'North Zone')->value('id');

        $this->actingAs($admin)->patch(route('delivery-zones.toggle', $zoneId))->assertSessionHas('status');

        $this->assertDatabaseHas('drivers', [
            'user_id' => $driverUser->id,
            'vehicle_number' => 'OPS-101',
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('warehouse_staff', [
            'user_id' => $warehouseUser->id,
            'warehouse_location' => 'Main Hub',
        ]);

        $this->assertDatabaseHas('delivery_zones', [
            'id' => $zoneId,
            'is_serviceable' => false,
        ]);
    }

    public function test_non_admin_is_forbidden_from_admin_ops_routes(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();

        $targetDriver = User::factory()->create([
            'role_id' => Role::query()->where('name', Role::DRIVER)->value('id'),
        ]);

        $this->actingAs($subscriber)
            ->post(route('drivers.store'), [
                'user_id' => $targetDriver->id,
                'vehicle_number' => 'FORBID-1',
                'is_active' => true,
            ])
            ->assertForbidden();
    }

    public function test_driver_role_is_forbidden_from_admin_ops_routes(): void
    {
        $this->seed();

        $driver = User::query()->where('email', 'driver@example.com')->firstOrFail();

        $this->actingAs($driver)
            ->get(route('drivers.index'))
            ->assertForbidden();
    }

    public function test_admin_can_assign_delivery_to_driver_from_control_panel(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $driver = User::query()->where('email', 'driver@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '220 Driver Board Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11444',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
        ])->assertRedirect(route('subscriptions.index'));

        $delivery = Delivery::query()
            ->where('address_id', $address->id)
            ->whereNull('driver_id')
            ->firstOrFail();

        $driverProfile = $driver->driver()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('drivers.index'))
            ->assertOk()
            ->assertSee('Driver Operations Control Panel');

        $this->actingAs($admin)
            ->patch(route('drivers.assign-delivery', $driverProfile), [
                'delivery_id' => $delivery->id,
            ])
            ->assertSessionHas('status');

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'driver_id' => $driverProfile->id,
        ]);
    }
}
