<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class RenewSubscriptions extends Command
{
    protected $signature = 'subscriptions:renew';

    protected $description = 'Renew due subscriptions and charge payments';

    public function handle(SubscriptionService $subscriptionService): int
    {
        $renewedCount = $subscriptionService->renewDueSubscriptions();

        $this->info("Renewed {$renewedCount} subscriptions.");

        return self::SUCCESS;
    }
}
