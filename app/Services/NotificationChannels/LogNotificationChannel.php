<?php

namespace App\Services\NotificationChannels;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class LogNotificationChannel implements NotificationChannel
{
    public function send(Notification $notification): void
    {
        Log::info('Dispatching notification', [
            'notification_id' => $notification->id,
            'user_id' => $notification->user_id,
            'type' => $notification->type,
            'event_type' => $notification->event_type,
            'subject' => $notification->subject,
        ]);
    }

    public function name(): string
    {
        return 'log';
    }
}
