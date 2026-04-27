<?php

namespace App\Services;

use App\Models\Box;
use App\Models\Item;

class WeightService
{
    /**
     * Get the shipping tier based on the total weight.
     */
    public function getTier(int $grams): string
    {
        $brackets = config('shipping.brackets', []);

        foreach ($brackets as $bracket) {
            if ($grams <= $bracket['max']) {
                return $bracket['name'];
            }
        }

        return 'oversized'; // Fallback
    }

    /**
     * Recalculate the total weight and update the box's shipping tier.
     */
    public function recalculate(Box $box): void
    {
        $totalWeight = $box->items->sum('weight_g');
        $tier = $this->getTier($totalWeight);

        $box->total_weight_g = $totalWeight;
        $box->shipping_tier = $tier;
        $box->save();
    }

    /**
     * Check if adding an item (and optionally removing one) would exceed the maximum weight.
     */
    public function wouldExceedLimit(Box $box, Item $adding, ?Item $removing = null): bool
    {
        $currentWeight = $box->total_weight_g;
        $addedWeight = $adding->weight_g ?? 0;
        $removedWeight = $removing ? ($removing->weight_g ?? 0) : 0;

        $newWeight = $currentWeight + $addedWeight - $removedWeight;

        return $newWeight > config('shipping.max_weight_g', 3000);
    }
}
