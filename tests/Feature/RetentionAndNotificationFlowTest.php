<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RetentionAndNotificationFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_retention_offer_can_be_presented_and_accepted(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();
        $plan = SubscriptionPlan::query()->where('name', 'standard')->firstOrFail();

        $address = Address::create([
            'user_id' => $subscriber->id,
            'street' => '14 Retention Street',
            'city' => 'Cairo',
            'region' => 'Cairo',
            'country' => 'EG',
            'postal_code' => '19000',
            'is_default' => true,
        ]);

        $this->actingAs($subscriber)->post(route('subscriptions.store'), [
            'plan_id' => $plan->id,
            'address_id' => $address->id,
            'start_date' => now()->toDateString(),
            'auto_renew' => 1,
            'eco_shipping' => 0,
        ])->assertRedirect(route('subscriptions.index'));

        $subscription = Subscription::query()->where('user_id', $subscriber->id)->firstOrFail();

        $this->actingAs($subscriber)->post(route('retention-offers.store', $subscription), [
            'offer_type' => 'discount',
            'offer_value' => '15%',
            'cancellation_reason' => 'Trying another plan',
        ])->assertSessionHas('status');

        $offerId = (string) \DB::table('retention_offers')->where('subscription_id', $subscription->id)->value('id');

        $this->actingAs($subscriber)->patch(route('retention-offers.update', $offerId), [
            'accepted' => true,
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('retention_offers', [
            'id' => $offerId,
            'accepted' => true,
        ]);
    }

    public function test_notifications_queue_command_marks_queued_rows_as_sent(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();

        Notification::create([
            'user_id' => $subscriber->id,
            'type' => 'email',
            'event_type' => 'delivery_update',
            'subject' => 'Queued Test',
            'body' => 'Queue message body',
            'status' => 'queued',
        ]);

        $this->artisan('notifications:process-queued')->assertExitCode(0);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $subscriber->id,
            'subject' => 'Queued Test',
            'status' => 'sent',
        ]);
    }
}
