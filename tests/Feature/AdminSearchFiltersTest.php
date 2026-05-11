<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminSearchFiltersTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_users_panel_applies_search_filters(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        User::factory()->create([
            'role_id' => Role::query()->where('name', Role::DRIVER)->value('id'),
            'name' => 'Filter Target User',
            'email' => 'filter-user@example.com',
            'must_change_password' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin-users.index', [
                'q' => 'Filter Target',
                'must_change_password' => '1',
            ]))
            ->assertOk()
            ->assertSee('filter-user@example.com');
    }

    public function test_admin_products_panel_applies_filters(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        Item::query()->create([
            'name' => 'Filter Product',
            'description' => 'X',
            'weight_g' => 100,
            'size_category' => 'small',
            'unit_price' => 10,
            'stock_qty' => 0,
            'is_limited_edition' => false,
            'is_addon' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('products.index', [
                'q' => 'Filter Product',
                'stock_state' => 'out_of_stock',
                'is_addon' => '1',
            ]))
            ->assertOk()
            ->assertSee('Filter Product');
    }

    public function test_admin_subscriptions_panel_applies_filters(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        Subscription::query()->create([
            'user_id' => $subscriber->id,
            'plan_id' => $plan->id,
            'status' => 'paused',
            'start_date' => now()->toDateString(),
            'next_billing_date' => now()->addDays(5)->toDateString(),
            'remaining_billing_days' => 5,
            'auto_renew' => false,
            'eco_shipping' => false,
            'loyalty_points' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('admin-subscriptions.index', [
                'q' => $subscriber->email,
                'status' => 'paused',
                'auto_renew' => '0',
            ]))
            ->assertOk()
            ->assertSee($subscriber->email);
    }
}
