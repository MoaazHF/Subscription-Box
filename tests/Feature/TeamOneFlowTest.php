<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TeamOneFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_register_as_a_subscriber(): void
    {
        $this->seed();

        $response = $this->post(route('register.store'), [
            'name' => 'Moaz Tester',
            'phone' => '01234567890',
            'email' => 'moaz@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'moaz@example.com',
        ]);
    }

    public function test_subscriber_can_create_a_subscription_and_payment_record(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $address = Address::create([
            'user_id' => $user->id,
            'street' => '42 Service Road',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11311',
            'is_default' => true,
        ]);

        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $response = $this->actingAs($user)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
        ]);

        $response->assertRedirect(route('subscriptions.index'));
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'subscription.created',
        ]);
    }

    public function test_audit_logs_page_is_restricted_to_admins(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($subscriber)
            ->get(route('audit-logs.index'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('audit-logs.index'))
            ->assertOk();
    }
}
