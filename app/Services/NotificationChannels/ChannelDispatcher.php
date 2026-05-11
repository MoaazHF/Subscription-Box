<?php

namespace App\Services\NotificationChannels;

use App\Models\Notification;
use RuntimeException;

class ChannelDispatcher
{
    public function __construct(
        private LogNotificationChannel $logNotificationChannel,
        private DatabaseNotificationChannel $databaseNotificationChannel
    ) {}

    public function dispatch(Notification $notification): string
    {
        if ($notification->type === 'failing_test') {
            throw new RuntimeException('Forced notification channel failure for testing.');
        }

        $channel = match ($notification->type) {
            'in_app' => $this->databaseNotificationChannel,
            default => $this->logNotificationChannel,
        };

        $channel->send($notification);

        return $channel->name();
    }
}
