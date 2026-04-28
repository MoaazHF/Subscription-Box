<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Delivery;
use App\Models\User;
use App\Notifications\DeliveryStatusChanged;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliveryStatusUpdateTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function makeDeliveryForUser(User $user, array $deliveryAttrs = []): Delivery
    {
        $address = Address::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'street' => '123 Main St',
            'city' => 'Cairo',
            'country' => 'Egypt',
        ]);

        return Delivery::create(array_merge([
            'id' => (string) Str::uuid(),
            'box_id' => (string) Str::uuid(),
            'address_id' => $address->id,
            'status' => 'pending',
        ], $deliveryAttrs));
    }

    public function test_allows_a_valid_status_transition(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user, ['status' => 'pending']);

        $this->actingAs($user)
            ->patch(route('deliveries.update', $delivery), ['status' => 'picking'])
            ->assertRedirect(route('deliveries.show', $delivery));

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'picking',
        ]);

        Notification::assertSentTo($user, DeliveryStatusChanged::class);
    }

    public function test_rejects_an_invalid_status_transition(): void
    {
        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user, ['status' => 'pending']);

        $this->actingAs($user)
            ->patch(route('deliveries.update', $delivery), ['status' => 'delivered'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'status' => 'pending',
        ]);
    }

    public function test_prevents_another_user_from_updating_the_delivery(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($owner, ['status' => 'pending']);

        $this->actingAs($other)
            ->patch(route('deliveries.update', $delivery), ['status' => 'picking'])
            ->assertForbidden();
    }

    public function test_allows_updating_delivery_instructions(): void
    {
        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user, ['status' => 'pending']);

        $this->actingAs($user)
            ->patch(route('deliveries.update', $delivery), [
                'status' => 'picking',
                'delivery_instructions' => 'Leave at the door.',
            ])
            ->assertRedirect(route('deliveries.show', $delivery));

        $this->assertDatabaseHas('deliveries', [
            'id' => $delivery->id,
            'delivery_instructions' => 'Leave at the door.',
        ]);
    }

    public function test_guest_cannot_update_delivery_status(): void
    {
        $user = User::factory()->create();
        $delivery = $this->makeDeliveryForUser($user);

        $this->patch(route('deliveries.update', $delivery), ['status' => 'picking'])
            ->assertRedirect(route('login'));
    }

    public function test_status_machine_transitions_are_correct(): void
    {
        $this->assertSame(['picking'], Delivery::STATUS_TRANSITIONS['pending']);
        $this->assertSame(['packed'], Delivery::STATUS_TRANSITIONS['picking']);
        $this->assertSame(['shipped'], Delivery::STATUS_TRANSITIONS['packed']);
        $this->assertSame(['out_for_delivery'], Delivery::STATUS_TRANSITIONS['shipped']);
        $this->assertSame(['delivered', 'undeliverable'], Delivery::STATUS_TRANSITIONS['out_for_delivery']);
        $this->assertSame([], Delivery::STATUS_TRANSITIONS['delivered']);
        $this->assertSame([], Delivery::STATUS_TRANSITIONS['undeliverable']);
    }
}
