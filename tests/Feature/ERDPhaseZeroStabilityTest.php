<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Delivery;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ERDPhaseZeroStabilityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_subscriptions_renew_command_renews_due_subscriptions(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $user->id,
            'street' => '10 Renew Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '12345',
            'is_default' => true,
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'status' => 'active',
            'start_date' => now()->subMonth()->toDateString(),
            'next_billing_date' => now()->subDay()->toDateString(),
            'remaining_billing_days' => 0,
            'auto_renew' => true,
            'eco_shipping' => false,
            'loyalty_points' => 0,
        ]);

        $this->artisan('subscriptions:renew')->assertExitCode(0);

        $this->assertDatabaseHas('payments', [
            'subscription_id' => $subscription->id,
            'gateway_reason_code' => 'subscription_renewed',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'remaining_billing_days' => 30,
        ]);
    }

    public function test_claim_route_accepts_damaged_type(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '42 Claim Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '54321',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
        ])->assertRedirect(route('subscriptions.index'));

        $delivery = Delivery::query()->where('address_id', $address->id)->firstOrFail();

        $this->actingAs($subscriber)
            ->post(route('deliveries.claims.store', $delivery), [
                'type' => 'damaged',
                'description' => 'Packaging was torn on arrival.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('claims', [
            'delivery_id' => $delivery->id,
            'type' => 'damaged',
        ]);
    }

    public function test_driver_can_mark_delivery_undeliverable_and_notification_is_queued(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $driverRoleId = Role::query()->where('name', Role::DRIVER)->value('id');
        $driverUser = User::factory()->create([
            'role_id' => $driverRoleId,
            'email' => 'driver@example.com',
        ]);

        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();
        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '55 Driver Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11311',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
        ])->assertRedirect(route('subscriptions.index'));

        $driverUser->driver()->create([
            'vehicle_number' => 'DR-1001',
            'is_active' => true,
        ]);

        $delivery = Delivery::query()->where('address_id', $address->id)->firstOrFail();
        $delivery->update([
            'driver_id' => $driverUser->driver->id,
            'status' => Delivery::OUT_FOR_DELIVERY,
        ]);

        $this->actingAs($driverUser)
            ->patch(route('driver.deliveries.status', $delivery), [
                'status' => 'undeliverable',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'undeliverable',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $subscriber->id,
            'type' => 'email',
            'event_type' => 'delivery_update',
            'status' => 'queued',
        ]);
    }
}
