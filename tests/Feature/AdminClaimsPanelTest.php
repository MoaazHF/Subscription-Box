<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Claim;
use App\Models\Delivery;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminClaimsPanelTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_review_and_resolve_and_reject_claims(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '91 Claims Admin Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11777',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
            'payment_gateway_status' => 'success',
            'payment_gateway_ref' => 'CLAIM-PANEL-REF',
            'payment_card_last4' => '4242',
            'payment_gateway_reason' => 'claims_panel_test',
        ])->assertRedirect(route('subscriptions.index'));

        $delivery = Delivery::query()->where('address_id', $address->id)->firstOrFail();

        $this->actingAs($subscriber)->post(route('deliveries.claims.store', $delivery), [
            'type' => 'damaged',
            'description' => 'Cap broken.',
        ])->assertSessionHas('success');

        $claim = Claim::query()->where('delivery_id', $delivery->id)->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin-claims.index', ['q' => 'Cap']))
            ->assertOk()
            ->assertSee('Claims Control Panel');

        $this->actingAs($admin)
            ->patch(route('admin-claims.resolve', $claim), [
                'resolution_notes' => 'Refund approved',
            ])
            ->assertSessionHas('status');

        $this->assertDatabaseHas('claims', [
            'id' => $claim->id,
            'status' => 'resolved',
            'resolved_by' => $admin->id,
            'resolution_notes' => 'Refund approved',
        ]);

        $pendingClaim = Claim::query()->create([
            'subscription_id' => $claim->subscription_id,
            'delivery_id' => $claim->delivery_id,
            'type' => 'missing',
            'description' => 'Second claim for rejection.',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->patch(route('admin-claims.reject', $pendingClaim), [
                'resolution_notes' => 'Insufficient evidence',
            ])
            ->assertSessionHas('status');

        $this->assertDatabaseHas('claims', [
            'id' => $pendingClaim->id,
            'status' => 'rejected',
            'resolved_by' => $admin->id,
        ]);
    }
}
