<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\FlashSale;
use App\Models\GiftSubscription;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class GrowthEngagementFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_growth_and_engagement_flows_work_end_to_end(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '90 Growth Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '22110',
            'is_default' => true,
        ]);

        $this->actingAs($admin)->post(route('promo-codes.store'), [
            'code' => 'SAVE20',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'max_uses' => 5,
            'expires_at' => now()->addDays(3)->toDateTimeString(),
        ])->assertSessionHas('status');

        $this->actingAs($subscriber)->post(route('promo-code-usages.store'), [
            'code' => 'SAVE20',
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('promo_code_usages', [
            'user_id' => $subscriber->id,
        ]);

        $this->actingAs($admin)->post(route('rewards.issue'), [
            'user_id' => $subscriber->id,
            'type' => 'loyalty_points',
            'points' => 25,
            'description' => 'Manual bonus',
        ])->assertSessionHas('status');

        $rewardId = (string) \DB::table('rewards')->where('user_id', $subscriber->id)->value('id');
        $this->actingAs($subscriber)->patch(route('rewards.apply', $rewardId))->assertSessionHas('status');

        $this->actingAs($subscriber)->post(route('gift-subscriptions.purchase'), [
            'plan_id' => $plan->id,
            'recipient_email' => 'gift-recipient@example.com',
            'recipient_name' => 'Gift User',
            'duration_months' => 3,
        ])->assertSessionHas('status');

        $gift = GiftSubscription::query()->latest('purchased_at')->firstOrFail();

        $recipient = User::factory()->create([
            'role_id' => Role::query()->where('name', Role::SUBSCRIBER)->value('id'),
            'email' => 'gift-recipient@example.com',
        ]);

        $recipientAddress = Address::create([
            'user_id' => $recipient->id,
            'street' => '77 Gift Lane',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '88888',
            'is_default' => true,
        ]);

        $this->actingAs($recipient)->post(route('gift-subscriptions.activate'), [
            'activation_code' => $gift->activation_code,
            'address_id' => $recipientAddress->id,
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('gift_subscriptions', [
            'id' => $gift->id,
            'status' => 'active',
            'recipient_user_id' => $recipient->id,
        ]);

        $this->actingAs($admin)->post(route('flash-sales.store'), [
            'plan_id' => $plan->id,
            'name' => 'Weekend Rush',
            'discount_percent' => 15,
            'stock_limit' => 10,
            'start_at' => now()->subHour()->toDateTimeString(),
            'end_at' => now()->addDay()->toDateTimeString(),
        ])->assertSessionHas('status');

        $flashSale = FlashSale::query()->latest('created_at')->firstOrFail();

        $this->actingAs($subscriber)
            ->post(route('flash-sales.claim', $flashSale))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('flash_sales', [
            'id' => $flashSale->id,
            'claimed_count' => 1,
        ]);

        $subscription = Subscription::query()->where('user_id', $subscriber->id)->first();

        if (! $subscription) {
            $this->actingAs($subscriber)->post(route('subscriptions.store'), [
                'plan_id' => $plan->id,
                'address_id' => $address->id,
                'start_date' => now()->toDateString(),
                'auto_renew' => 1,
                'eco_shipping' => 0,
            ]);

            $subscription = Subscription::query()->where('user_id', $subscriber->id)->firstOrFail();
        }

        $boxId = (string) $subscription->boxes()->value('id');

        $this->actingAs($subscriber)->post(route('social-posts.store'), [
            'box_id' => $boxId,
            'caption' => 'Monthly reveal',
            'photo_url' => 'https://example.com/image.jpg',
            'visibility' => 'public',
        ])->assertSessionHas('status');

        $postId = (string) \DB::table('social_posts')->where('user_id', $subscriber->id)->value('id');

        $this->actingAs($subscriber)
            ->delete(route('social-posts.destroy', $postId))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('social_posts', [
            'id' => $postId,
            'is_deleted' => true,
        ]);
    }
}
