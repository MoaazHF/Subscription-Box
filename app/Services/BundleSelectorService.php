<?php

namespace App\Services;

use App\Models\Box;
use App\Models\BoxItem;
use App\Models\Bundle;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BundleSelectorService
{
    public function __construct(
        private StockAllocationService $stockAllocationService,
        private WeightService $weightService
    ) {}

    public function applyBundle(Box $box, Bundle $bundle): int
    {
        if ($box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast())) {
            throw new RuntimeException('This box is locked and cannot be modified.');
        }

        $bundle->load(['bundleItems.item']);

        if ($bundle->bundleItems->isEmpty()) {
            throw new RuntimeException('Selected bundle has no items.');
        }

        return DB::transaction(function () use ($box, $bundle): int {
            $existingBundleItems = BoxItem::query()
                ->where('box_id', $box->id)
                ->whereNotNull('bundle_id')
                ->with('item')
                ->get();

            foreach ($existingBundleItems as $existingBundleItem) {
                if ($existingBundleItem->item) {
                    $this->stockAllocationService->release($existingBundleItem->item, max(1, $existingBundleItem->quantity));
                }

                $existingBundleItem->delete();
            }

            foreach ($bundle->bundleItems as $bundleItem) {
                $alreadyInBox = BoxItem::query()
                    ->where('box_id', $box->id)
                    ->where('item_id', $bundleItem->item_id)
                    ->exists();

                if ($alreadyInBox) {
                    throw new RuntimeException('Cannot apply bundle because one or more bundle items are already in the box.');
                }

                $item = $bundleItem->item;

                if (! $item) {
                    throw new RuntimeException('Bundle contains an invalid catalog item.');
                }

                $this->stockAllocationService->reserve($item, $bundleItem->quantity);

                BoxItem::query()->create([
                    'box_id' => $box->id,
                    'item_id' => $bundleItem->item_id,
                    'bundle_id' => $bundle->id,
                    'quantity' => $bundleItem->quantity,
                    'is_addon' => false,
                    'is_swapped' => false,
                    'is_surprise' => false,
                    'added_at' => now(),
                ]);
            }

            $box->load('items');
            $this->weightService->recalculate($box);

            return $bundle->bundleItems->count();
        });
    }
}
