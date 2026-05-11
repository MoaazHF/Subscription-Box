<?php

namespace App\Services;

use App\Models\Box;
use App\Models\BoxItem;
use App\Models\Item;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class BoxCustomizationService
{
    public function __construct(
        private WeightService $weightService,
        private ThemeRotationService $themeRotationService,
        private StockAllocationService $stockAllocationService
    ) {}

    public function swap(
        Box $box,
        BoxItem $outItem,
        Item $newItem,
        User $user,
        bool $confirmRotation = false,
        bool $confirmAllergen = false
    ): array {
        if ($box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast())) {
            throw new Exception('This box is locked and cannot be customized.');
        }

        if ($outItem->item_id === $newItem->id) {
            throw new Exception('Swap target item must be different from the removed item.');
        }

        $box->loadMissing('items');

        if ($box->items->where('id', '!=', $outItem->item_id)->contains('id', $newItem->id)) {
            throw new Exception('This item is already in your box. Duplicates are not allowed.');
        }

        $removingItem = Item::query()->find($outItem->item_id);

        if (! $removingItem) {
            throw new Exception('Removed item no longer exists.');
        }

        if ($this->weightService->wouldExceedLimit($box, $newItem, $removingItem)) {
            throw new Exception('Swapping this item would exceed the 3000g maximum weight limit.');
        }

        if ($this->themeRotationService->wasInPreviousBox($box, $newItem->id) && ! $confirmRotation) {
            return [
                'status' => 'warning',
                'type' => 'rotation',
                'message' => 'You received this item as a surprise in your previous box. Are you sure you want it again?',
            ];
        }

        $hasConflict = DB::table('user_allergens')
            ->join('item_allergens', 'user_allergens.allergen_tag_id', '=', 'item_allergens.allergen_tag_id')
            ->where('user_allergens.user_id', $user->id)
            ->where('item_allergens.item_id', $newItem->id)
            ->exists();

        if ($hasConflict && ! $confirmAllergen) {
            return [
                'status' => 'warning',
                'type' => 'allergen',
                'message' => 'This item contains allergens that conflict with your profile.',
            ];
        }

        DB::transaction(function () use ($outItem, $newItem, $box, $removingItem): void {
            $this->stockAllocationService->reserve($newItem, max(1, $outItem->quantity));
            $this->stockAllocationService->release($removingItem, max(1, $outItem->quantity));

            $outItem->update([
                'is_swapped' => true,
                'item_id' => $newItem->id,
                'bundle_id' => null,
                'updated_at' => now(),
            ]);

            $box->load('items');
            $this->weightService->recalculate($box);
        });

        return [
            'status' => 'success',
            'message' => 'Item swapped successfully!',
        ];
    }

    public function add(Box $box, Item $newItem, User $user, bool $confirmAllergen = false): array
    {
        if ($box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast())) {
            throw new Exception('This box is locked and cannot be customized.');
        }

        $box->loadMissing('items');

        if ($box->items->contains('id', $newItem->id)) {
            throw new Exception('This item is already in your box. Duplicates are not allowed.');
        }

        if ($this->weightService->wouldExceedLimit($box, $newItem)) {
            throw new Exception('Adding this item would exceed the 3000g maximum weight limit.');
        }

        $hasConflict = DB::table('user_allergens')
            ->join('item_allergens', 'user_allergens.allergen_tag_id', '=', 'item_allergens.allergen_tag_id')
            ->where('user_allergens.user_id', $user->id)
            ->where('item_allergens.item_id', $newItem->id)
            ->exists();

        if ($hasConflict && ! $confirmAllergen) {
            return [
                'status' => 'warning',
                'type' => 'allergen',
                'message' => 'This item contains allergens that conflict with your profile.',
            ];
        }

        DB::transaction(function () use ($newItem, $box): void {
            $this->stockAllocationService->reserve($newItem);

            BoxItem::create([
                'box_id' => $box->id,
                'item_id' => $newItem->id,
                'quantity' => 1,
                'is_addon' => true,
                'is_swapped' => false,
                'added_at' => now(),
            ]);

            $box->load('items');
            $this->weightService->recalculate($box);
        });

        return [
            'status' => 'success',
            'message' => 'Add-on item added successfully!',
        ];
    }

    public function remove(Box $box, BoxItem $boxItem): void
    {
        $item = Item::query()->findOrFail($boxItem->item_id);

        DB::transaction(function () use ($box, $boxItem, $item): void {
            $this->stockAllocationService->release($item, max(1, $boxItem->quantity));
            $boxItem->delete();

            $box->load('items');
            $this->weightService->recalculate($box);
        });
    }
}
