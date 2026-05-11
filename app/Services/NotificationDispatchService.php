<?php

namespace App\Services;

use App\Jobs\DispatchNotificationJob;
use App\Models\Notification;
use App\Services\NotificationChannels\ChannelDispatcher;
use Throwable;

class NotificationDispatchService
{
    public function __construct(private ChannelDispatcher $channelDispatcher) {}

    public function dispatchQueuedBatch(int $limit = 100): int
    {
        $notifications = Notification::query()
            ->where('status', Notification::QUEUED)
            ->where(function ($query): void {
                $query->whereNull('retry_count')
                    ->orWhere('retry_count', '<', config('queue.notifications_max_retries', 3));
            })
            ->oldest('created_at')
            ->limit($limit)
            ->get();

        foreach ($notifications as $notification) {
            $notification->update([
                'status' => Notification::PROCESSING,
                'processed_at' => now(),
            ]);

            DispatchNotificationJob::dispatch($notification->id);
        }

        return $notifications->count();
    }

    public function deliver(Notification $notification): void
    {
        if ($notification->status === Notification::SENT) {
            return;
        }

        try {
            $channel = $this->channelDispatcher->dispatch($notification);

            $notification->update([
                'status' => Notification::SENT,
                'channel' => $channel,
                'last_error' => null,
                'sent_at' => now(),
                'processed_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $retryCount = (int) $notification->retry_count + 1;
            $maxRetries = (int) config('queue.notifications_max_retries', 3);

            $notification->update([
                'status' => $retryCount >= $maxRetries ? Notification::FAILED : Notification::QUEUED,
                'retry_count' => $retryCount,
                'last_error' => mb_substr($exception->getMessage(), 0, 1000),
                'processed_at' => now(),
            ]);
        }
    }
}
