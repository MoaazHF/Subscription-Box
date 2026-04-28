<?php

namespace App\Http\Controllers;

use App\Http\Requests\SwapItemRequest;
use App\Models\Box;
use App\Models\BoxItem;
use App\Models\Item;
use App\Services\BoxCustomizationService;
use App\Services\WeightService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BoxCustomizationController extends Controller
{
    public function __construct(
        private BoxCustomizationService $customizationService,
        private WeightService $weightService
    ) {}

    /**
     * Render the customize view for a given box.
     */
    public function show(Request $request, Box $box): View
    {
        $box->load(['items', 'subscription']);
        abort_unless($request->user()->isAdmin() || $box->ownedBy($request->user()), Response::HTTP_FORBIDDEN);

        $availableItems = Item::where('stock_qty', '>', 0)->get();

        return view('boxes.customize', compact('box', 'availableItems'));
    }

    /**
     * Handle the swapping of items.
     */
    public function swap(SwapItemRequest $request, Box $box): RedirectResponse
    {
        $box->load('subscription');
        abort_unless($request->user()->isAdmin() || $box->ownedBy($request->user()), Response::HTTP_FORBIDDEN);

        $outItem = BoxItem::findOrFail($request->validated('remove_box_item_id'));
        $newItem = Item::findOrFail($request->validated('new_item_id'));

        abort_unless($outItem->box_id === $box->id, Response::HTTP_FORBIDDEN);

        try {
            $result = $this->customizationService->swap($box, $outItem, $newItem, $request->user());

            if ($result['status'] === 'warning') {
                return back()->with('swap_warning', [
                    'type' => $result['type'],
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
    public function remove(Request $request, Box $box, BoxItem $boxItem): RedirectResponse
    {
        $box->load('subscription');
        abort_unless($request->user()->isAdmin() || $box->ownedBy($request->user()), Response::HTTP_FORBIDDEN);
        abort_unless($boxItem->box_id === $box->id, Response::HTTP_FORBIDDEN);

        if ($box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast())) {
            return back()->with('error', 'Box is locked and cannot be modified.');
        }

        DB::table('box_items')->where('id', $boxItem->id)->delete();

        $box->load('items');
        $this->weightService->recalculate($box);

        return back()->with('success', 'Item removed successfully.');
    }
}
