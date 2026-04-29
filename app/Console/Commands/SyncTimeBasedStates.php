<?php

namespace App\Console\Commands;

use App\Models\GiftSubscription;
use Illuminate\Console\Command;

class SyncTimeBasedStates extends Command
{
    protected $signature = 'lifecycle:sync-time-based';

    protected $description = 'Sync time-based lifecycle states like gift subscription expiration';

    public function handle(): int
    {
        $expired = GiftSubscription::query()
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        $this->info("Expired {$expired} gift subscriptions.");

        return self::SUCCESS;
    }
}
