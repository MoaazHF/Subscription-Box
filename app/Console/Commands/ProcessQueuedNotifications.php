<?php

namespace App\Console\Commands;

use App\Services\NotificationDispatchService;
use Illuminate\Console\Command;

class ProcessQueuedNotifications extends Command
{
    protected $signature = 'notifications:process-queued';

    protected $description = 'Process queued notifications and mark them as sent';

    public function handle(NotificationDispatchService $notificationDispatchService): int
    {
        $processedCount = $notificationDispatchService->dispatchQueuedBatch();

        $this->info("Dispatched {$processedCount} queued notifications.");

        return self::SUCCESS;
    }
}
