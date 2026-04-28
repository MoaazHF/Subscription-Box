<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Delivery;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TeamThreeFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_delivery_is_created_when_a_subscription_starts(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '12 Delivery Lane',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11728',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 1,
        ])->assertRedirect(route('subscriptions.index'));

        $delivery = Delivery::query()
            ->where('address_id', $address->id)
            ->firstOrFail();

        $this->actingAs($subscriber)
            ->get(route('deliveries.index'))
            ->assertOk()
            ->assertSee($delivery->tracking_number);

        $this->actingAs($subscriber)
            ->get(route('deliveries.show', $delivery))
            ->assertOk()
            ->assertSee($delivery->tracking_number);

        $this->actingAs($subscriber)
            ->patch(route('deliveries.update-status', $delivery), [
                'status' => Delivery::OUT_FOR_DELIVERY,
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('deliveries.update-status', $delivery), [
                'status' => Delivery::OUT_FOR_DELIVERY,
                'tracking_number' => $delivery->tracking_number,
                'estimated_delivery' => now()->addDay()->toDateString(),
                'delivery_instructions' => 'Call on arrival.',
                'eco_dispatch' => 1,
            ])
            ->assertRedirect(route('deliveries.show', $delivery));

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => Delivery::OUT_FOR_DELIVERY,
            'delivery_instructions' => 'Call on arrival.',
        ]);
    }
}
