<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Delivery;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DriverProgressSliderFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_driver_can_update_delivery_progress_with_slider_step(): void
    {
        $this->seed();

        [$subscriber, $driverUser, $delivery] = $this->provisionDeliveryForDriver();

        $this->actingAs($driverUser)
            ->patch(route('driver.deliveries.status', $delivery), [
                'progress_step' => 4,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => Delivery::OUT_FOR_DELIVERY,
        ]);

        $this->actingAs($subscriber)
            ->get(route('deliveries.index'))
            ->assertOk()
            ->assertSee('85%');
    }

    public function test_driver_cannot_move_progress_backwards_with_slider_step(): void
    {
        $this->seed();

        [, $driverUser, $delivery] = $this->provisionDeliveryForDriver(Delivery::SHIPPED);

        $this->actingAs($driverUser)
            ->from(route('driver.index'))
            ->patch(route('driver.deliveries.status', $delivery), [
                'progress_step' => 1,
            ])
            ->assertSessionHasErrors('status');
    }

    /** @return array{0: User, 1: User, 2: Delivery} */
    private function provisionDeliveryForDriver(string $status = Delivery::PENDING): array
    {
        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $driverRoleId = Role::query()->where('name', Role::DRIVER)->value('id');
        $driverUser = User::factory()->create([
            'role_id' => $driverRoleId,
            'email' => 'slider-driver@example.com',
        ]);
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '31 Slider Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11531',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
            'payment_gateway_status' => 'success',
            'payment_gateway_ref' => 'SLIDER-DRIVER-REF',
            'payment_card_last4' => '4242',
            'payment_gateway_reason' => 'slider_driver_test',
        ])->assertRedirect(route('subscriptions.index'));

        $driverUser->driver()->create([
            'vehicle_number' => 'DR-3101',
            'is_active' => true,
        ]);

        $delivery = Delivery::query()->where('address_id', $address->id)->firstOrFail();
        $delivery->update([
            'driver_id' => $driverUser->driver->id,
            'status' => $status,
        ]);

        return [$subscriber, $driverUser, $delivery];
    }
}
