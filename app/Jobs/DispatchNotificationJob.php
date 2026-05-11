<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\NotificationDispatchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DispatchNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [1, 5, 10];

    public function __construct(public string $notificationId)
    {
        $this->onQueue('default');
    }

    public function handle(NotificationDispatchService $dispatchService): void
    {
        $notification = Notification::query()->find($this->notificationId);

        if (! $notification) {
            return;
        }

        $dispatchService->deliver($notification);
    }
}
