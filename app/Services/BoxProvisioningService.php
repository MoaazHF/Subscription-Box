<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Subscription;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BoxProvisioningService
{
    public function __construct(
        private WeightService $weightService,
        private DeliveryProvisioningService $deliveryProvisioningService
    ) {}

    public function provisionCurrentBox(Subscription $subscription): void
    {
        $subscription->loadMissing('plan');

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $box = $subscription->boxes()->firstOrCreate(
            [
                'period_month' => $currentMonth,
                'period_year' => $currentYear,
            ],
            [
                'status' => 'open',
                'lock_date' => now()->addDays(5)->toDateString(),
                'theme' => ucfirst($subscription->plan->name).' Starter Box',
                'total_weight_g' => 0,
                'shipping_tier' => 'standard',
            ]
        );

        $this->deliveryProvisioningService->provisionForBox($box, $subscription);

        if ($box->items()->exists()) {
            return;
        }

        $starterItems = $this->starterItemsFor($subscription);

        if ($starterItems->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($box, $starterItems): void {
            foreach ($starterItems as $item) {
                $box->items()->attach($item->id, [
                    'id' => (string) Str::uuid(),
                    'quantity' => 1,
                    'is_addon' => false,
                    'is_swapped' => false,
                    'is_surprise' => false,
                    'added_at' => now(),
                ]);
            }

            $box->load('items');
            $this->weightService->recalculate($box);
        });
    }

    /**
     * @return Collection<int, Item>
     */
    private function starterItemsFor(Subscription $subscription): Collection
    {
        $items = Item::query()
            ->where('stock_qty', '>', 0)
            ->orderBy('name')
            ->get();

        $selectedItems = collect();
        $selectedWeight = 0;
        $itemLimit = $subscription->plan->max_items;
        $weightLimit = $subscription->plan->max_weight_g;

        foreach ($items as $item) {
            if ($selectedItems->count() >= $itemLimit) {
                break;
            }

            $nextWeight = $selectedWeight + $item->weight_g;

            if ($nextWeight > $weightLimit) {
                continue;
            }

            $selectedItems->push($item);
            $selectedWeight = $nextWeight;
        }

        return $selectedItems;
    }
}
