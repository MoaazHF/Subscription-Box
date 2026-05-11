<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class NotificationHeaderPopupTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_user_sees_notification_popup_and_counter(): void
    {
        $this->seed();
        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        Notification::query()->create([
            'user_id' => $user->id,
            'type' => 'email',
            'event_type' => 'delivery_update',
            'subject' => 'Delivery moved to out for delivery',
            'body' => 'Your delivery is on the way.',
            'status' => Notification::QUEUED,
        ]);

        Notification::query()->create([
            'user_id' => $user->id,
            'type' => 'email',
            'event_type' => 'payment_update',
            'subject' => 'Payment approved',
            'body' => 'Payment completed successfully.',
            'status' => Notification::SENT,
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('data-notification-popup', false)
            ->assertSee('data-notification-count', false)
            ->assertSee('Delivery moved to out for delivery')
            ->assertSee('Payment approved');
    }
}
