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
        private ThemeRotationService $themeRotationService
    ) {}

    /**
     * Swap an item in the box with a new item, adhering to weight and lock rules.
     */
    public function swap(Box $box, BoxItem $outItem, Item $newItem, User $user): array
    {
        // 1. F15 Lock check
        if ($box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast())) {
            throw new Exception('This box is locked and cannot be customized.');
        }

        // 2. F13 Weight check
        $removingItem = DB::table('items')->where('id', $outItem->item_id)->first();
        // Since we need an Item model for wouldExceedLimit:
        $removingItemModel = Item::find($outItem->item_id);

        if ($this->weightService->wouldExceedLimit($box, $newItem, $removingItemModel)) {
            throw new Exception('Swapping this item would exceed the 3000g maximum weight limit.');
        }

        // 3a. Theme rotation check (F22)
        $request = request();
        $confirmRotation = $request->boolean('confirm_rotation');
        if ($this->themeRotationService->wasInPreviousBox($box, $newItem->id) && ! $confirmRotation) {
            return [
                'status' => 'warning',
                'type' => 'rotation',
                'message' => 'You received this item as a surprise in your previous box. Are you sure you want it again?',
            ];
        }

        // 3b. Allergen conflict check
        $confirmAllergen = $request->boolean('confirm_allergen');

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

        // 4. DB Transaction
        DB::transaction(function () use ($outItem, $newItem, $box) {
            DB::table('box_items')->where('id', $outItem->id)->update([
                'is_swapped' => true,
                'item_id' => $newItem->id,
                'updated_at' => now(),
            ]);

            // Refresh the box items relation so recalculate uses the updated data
            $box->load('items');

            // 5. Call WeightService::recalculate()
            $this->weightService->recalculate($box);
        });

        return [
            'status' => 'success',
            'message' => 'Item swapped successfully!',
        ];
    }
}
