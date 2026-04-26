<?php

namespace App\Http\Controllers;

use App\Http\Requests\SwapItemRequest;
use App\Models\Box;
use App\Models\BoxItem;
use App\Models\Item;
use App\Services\BoxCustomizationService;
use App\Services\WeightService;
use Illuminate\Support\Facades\DB;

class BoxCustomizationController extends Controller
{
    public function __construct(
        private BoxCustomizationService $customizationService,
        private WeightService $weightService
    ) {}

    /**
     * Render the customize view for a given box.
     */
    public function show(Box $box)
    {
        $box->load('items');

        // Items available to swap in
        $availableItems = Item::where('stock_qty', '>', 0)->get();

        return view('boxes.customize', compact('box', 'availableItems'));
    }

    /**
     * Handle the swapping of items.
     */
    public function swap(SwapItemRequest $request, Box $box)
    {
        $outItem = BoxItem::findOrFail($request->validated('remove_box_item_id'));
        $newItem = Item::findOrFail($request->validated('new_item_id'));

        // Provide the authenticated user (mock fallback if needed in this phase)
        $user = $request->user();

        try {
            $result = $this->customizationService->swap($box, $outItem, $newItem, $user);

            if ($result['status'] === 'warning') {
                return back()->with('swap_warning', [
                    'message' => $result['message'],
                    'remove_box_item_id' => $outItem->id,
                    'new_item_id' => $newItem->id,
                    'new_item_name' => $newItem->name,
                ]);
            }

            return back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove an item from the box.
     */
    public function remove(Box $box, BoxItem $boxItem)
    {
        if ($box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast())) {
            return back()->with('error', 'Box is locked and cannot be modified.');
        }

        DB::table('box_items')->where('id', $boxItem->id)->delete();

        $box->load('items');
        $this->weightService->recalculate($box);

        return back()->with('success', 'Item removed successfully.');
    }
}
