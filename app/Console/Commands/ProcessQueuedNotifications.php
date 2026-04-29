<?php

namespace App\Console\Commands;

use App\Services\NotificationQueueService;
use Illuminate\Console\Command;

class ProcessQueuedNotifications extends Command
{
    protected $signature = 'notifications:process-queued';

    protected $description = 'Process queued notifications and mark them as sent';

    public function handle(NotificationQueueService $notificationQueueService): int
    {
        $processedCount = $notificationQueueService->processQueued();

        $this->info("Processed {$processedCount} queued notifications.");

        return self::SUCCESS;
    }
}
