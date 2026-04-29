<?php

namespace App\Services;

use App\Models\Notification;

class NotificationQueueService
{
    public function processQueued(): int
    {
        $notifications = Notification::query()
            ->where('status', 'queued')
            ->limit(100)
            ->get();

        foreach ($notifications as $notification) {
            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        return $notifications->count();
    }
}
