<?php

namespace App\Services;

use App\Models\Box;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

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
        $totalWeight = (int) DB::table('box_items')
            ->join('items', 'items.id', '=', 'box_items.item_id')
            ->where('box_items.box_id', $box->id)
            ->selectRaw('COALESCE(SUM(items.weight_g * box_items.quantity), 0) as total_weight')
            ->value('total_weight');

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
