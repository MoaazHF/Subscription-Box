<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DriverDeliveryTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array{Driver, Delivery, User}
     */
    private function makeDriverWithDelivery(): array
    {
        $driver = Driver::create([
            'id' => (string) Str::uuid(),
            'name' => 'Test Driver',
            'phone' => '010-000-0000',
            'vehicle_plate' => 'AB-1234',
            'is_available' => true,
        ]);

        $user = User::factory()->create();

        $address = Address::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'street' => '99 Nile St',
            'city' => 'Cairo',
            'country' => 'Egypt',
        ]);

        $delivery = Delivery::create([
            'id' => (string) Str::uuid(),
            'box_id' => (string) Str::uuid(),
            'address_id' => $address->id,
            'driver_id' => $driver->id,
            'status' => 'out_for_delivery',
        ]);

        return [$driver, $delivery, $user];
    }

    public function test_driver_can_view_their_delivery_queue(): void
    {
        [$driver] = $this->makeDriverWithDelivery();

        $this->get(route('driver.index', ['driver_id' => $driver->id]))
            ->assertOk()
            ->assertSee($driver->name);
    }

    public function test_driver_can_view_an_assigned_delivery(): void
    {
        [$driver, $delivery] = $this->makeDriverWithDelivery();

        $this->get(route('driver.show', ['delivery' => $delivery, 'driver_id' => $driver->id]))
            ->assertOk()
            ->assertSee('Update Status');
    }

    public function test_driver_cannot_view_another_drivers_delivery(): void
    {
        [, $delivery] = $this->makeDriverWithDelivery();

        $this->get(route('driver.show', ['delivery' => $delivery, 'driver_id' => (string) Str::uuid()]))
            ->assertForbidden();
    }

    public function test_driver_can_update_delivery_status_to_delivered(): void
    {
        [$driver, $delivery] = $this->makeDriverWithDelivery();

        $this->patch(route('driver.update', $delivery), [
            'driver_id' => $driver->id,
            'status' => 'delivered',
        ])->assertRedirect();

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'delivered',
        ]);
    }

    public function test_driver_cannot_skip_status_steps(): void
    {
        [$driver, $delivery] = $this->makeDriverWithDelivery();

        // out_for_delivery → packed is not a valid transition
        $this->patch(route('driver.update', $delivery), [
            'driver_id' => $driver->id,
            'status' => 'packed',
        ])->assertSessionHasErrors('status');
    }

    public function test_wrong_driver_cannot_update_delivery_status(): void
    {
        [, $delivery] = $this->makeDriverWithDelivery();

        $this->patch(route('driver.update', $delivery), [
            'driver_id' => (string) Str::uuid(),
            'status' => 'delivered',
        ])->assertForbidden();
    }
}
