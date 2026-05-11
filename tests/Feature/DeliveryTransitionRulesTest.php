<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Delivery;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DeliveryTransitionRulesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_cannot_apply_invalid_delivery_transition(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '70 Transition Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11555',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
            'payment_gateway_status' => 'success',
            'payment_gateway_ref' => 'TRANSITION-REF',
            'payment_card_last4' => '4242',
            'payment_gateway_reason' => 'transition_test',
        ])->assertRedirect(route('subscriptions.index'));

        $delivery = Delivery::query()->where('address_id', $address->id)->firstOrFail();

        $this->actingAs($admin)
            ->from(route('deliveries.show', $delivery))
            ->patch(route('deliveries.update-status', $delivery), [
                'status' => Delivery::DELIVERED,
            ])
            ->assertSessionHasErrors('status');
    }
}
