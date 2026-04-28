<?php

namespace App\Console\Commands;

use App\Models\Claim;
use App\Models\Delivery;
use Illuminate\Console\Command;

class CheckMissingDeliveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delivery:check-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for deliveries that are overdue and flag them as missing or delayed.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for missing or delayed deliveries...');

        // Find deliveries that have an estimated_delivery date in the past
        // and are not yet delivered, failed, or canceled.
        $overdueDeliveries = Delivery::whereIn('status', ['shipped', 'out_for_delivery'])
            ->whereNotNull('estimated_delivery')
            ->where('estimated_delivery', '<', now()->subHours(24)) // 24 hours overdue
            ->get();

        $count = 0;
        foreach ($overdueDeliveries as $delivery) {
            // Flag as failed/delayed or trigger a notification/claim
            // For now, let's create an automated missing claim
            $existingClaim = Claim::where('delivery_id', $delivery->id)
                ->where('type', 'missing')
                ->exists();

            if (! $existingClaim) {
                Claim::create([
                    'subscription_id' => $delivery->box->subscription_id,
                    'delivery_id' => $delivery->id,
                    'type' => 'missing',
                    'description' => 'Automated claim: Delivery is more than 24 hours overdue.',
                    'status' => 'pending',
                    'submitted_at' => now(),
                ]);
                $count++;
            }
        }

        $this->info("Found and flagged {$count} overdue deliveries.");
    }
}
