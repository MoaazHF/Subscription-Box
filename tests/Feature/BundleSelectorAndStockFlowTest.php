<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Box;
use App\Models\Bundle;
use App\Models\BundleItem;
use App\Models\Item;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\BundleSelectorService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class BundleSelectorAndStockFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_bundle_can_be_applied_to_box_and_stock_is_decremented(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '51 Bundle Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11001',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
            'payment_gateway_status' => 'success',
            'payment_gateway_ref' => 'BUNDLE-STOCK-REF',
            'payment_card_last4' => '4242',
            'payment_gateway_reason' => 'bundle_stock_test',
        ])->assertRedirect(route('subscriptions.index'));

        $subscription = Subscription::query()->where('user_id', $subscriber->id)->firstOrFail();
        $box = Box::query()->create([
            'subscription_id' => $subscription->id,
            'period_month' => ((int) now()->month % 12) + 1,
            'period_year' => (int) now()->year + ((int) now()->month === 12 ? 1 : 0),
            'status' => 'open',
            'lock_date' => now()->addDays(5)->toDateString(),
            'theme' => 'Bundle Test Box',
            'total_weight_g' => 0,
            'shipping_tier' => 'standard',
        ]);

        $item = Item::query()->create([
            'name' => 'Bundle Item One',
            'description' => 'Item for bundle test',
            'weight_g' => 200,
            'size_category' => 'small',
            'unit_price' => 9.99,
            'stock_qty' => 5,
            'is_limited_edition' => false,
            'is_addon' => false,
        ]);

        $bundle = Bundle::query()->create([
            'name' => 'Test Bundle',
            'description' => 'One item bundle',
            'is_active' => true,
        ]);

        BundleItem::query()->create([
            'bundle_id' => $bundle->id,
            'item_id' => $item->id,
            'quantity' => 2,
        ]);

        app(BundleSelectorService::class)->applyBundle($box, $bundle);

        $this->assertDatabaseHas('box_items', [
            'box_id' => $box->id,
            'item_id' => $item->id,
            'bundle_id' => $bundle->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock_qty' => 3,
        ]);
    }

    public function test_limited_stock_prevents_add_when_exhausted(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '52 Stock Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '11002',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
            'payment_gateway_status' => 'success',
            'payment_gateway_ref' => 'LIMIT-STOCK-REF',
            'payment_card_last4' => '4242',
            'payment_gateway_reason' => 'limit_stock_test',
        ])->assertRedirect(route('subscriptions.index'));

        $box = Box::query()->whereHas('subscription', fn ($query) => $query->where('user_id', $subscriber->id))->firstOrFail();

        $item = Item::query()->create([
            'name' => 'No Stock Item',
            'description' => 'Out of stock',
            'weight_g' => 150,
            'size_category' => 'small',
            'unit_price' => 5,
            'stock_qty' => 0,
            'is_limited_edition' => true,
            'limited_stock' => 0,
            'is_addon' => true,
        ]);

        $this->actingAs($subscriber)
            ->post(route('boxes.add', $box), ['new_item_id' => $item->id])
            ->assertSessionHas('error');
    }
}
