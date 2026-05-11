<?php

namespace App\Services\NotificationChannels;

use App\Models\Notification;

class DatabaseNotificationChannel implements NotificationChannel
{
    public function send(Notification $notification): void
    {
        $notification->update([
            'processed_at' => now(),
        ]);
    }

    public function name(): string
    {
        return 'database';
    }
}
