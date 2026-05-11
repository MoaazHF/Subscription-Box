<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationDispatchFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_queue_command_dispatches_queued_notifications_to_sent(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();

        Notification::query()->create([
            'user_id' => $subscriber->id,
            'type' => 'email',
            'event_type' => 'delivery_update',
            'subject' => 'Queued Notification',
            'body' => 'Body',
            'status' => Notification::QUEUED,
            'idempotency_key' => Str::uuid()->toString(),
        ]);

        $this->artisan('notifications:process-queued')->assertExitCode(0);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $subscriber->id,
            'subject' => 'Queued Notification',
            'status' => Notification::SENT,
            'channel' => 'log',
        ]);
    }

    public function test_notification_is_marked_failed_after_retry_limit(): void
    {
        $this->seed();

        $subscriber = User::query()->where('email', 'test@example.com')->firstOrFail();

        $notification = Notification::query()->create([
            'user_id' => $subscriber->id,
            'type' => 'failing_test',
            'event_type' => 'delivery_update',
            'subject' => 'Failing Notification',
            'body' => 'Body',
            'status' => Notification::QUEUED,
            'idempotency_key' => Str::uuid()->toString(),
        ]);

        $this->artisan('notifications:process-queued')->assertExitCode(0);
        $this->artisan('notifications:process-queued')->assertExitCode(0);
        $this->artisan('notifications:process-queued')->assertExitCode(0);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'status' => Notification::FAILED,
            'retry_count' => 3,
        ]);
    }
}
