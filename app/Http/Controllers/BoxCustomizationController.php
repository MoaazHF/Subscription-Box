<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBoxItemRequest;
use App\Http\Requests\ApplyBundleToBoxRequest;
use App\Http\Requests\SwapItemRequest;
use App\Models\Box;
use App\Models\BoxItem;
use App\Models\Bundle;
use App\Models\Item;
use App\Services\BoxCustomizationService;
use App\Services\BundleSelectorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BoxCustomizationController extends Controller
{
    public function __construct(
        private BoxCustomizationService $customizationService,
        private BundleSelectorService $bundleSelectorService
    ) {}

    public function show(Request $request, Box $box): View
    {
        $box->load(['items', 'subscription']);

        abort_unless($request->user()->isAdmin() || $box->ownedBy($request->user()), Response::HTTP_FORBIDDEN);

        $availableItems = Item::query()->where('stock_qty', '>', 0)->orderBy('name')->get();

        $availableBundles = Bundle::query()
            ->withCount('bundleItems')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $isLocked = $box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast());
        $hoursUntilLock = $box->lock_date ? now()->diffInHours($box->lock_date, false) : 999;
        $weightPercent = min(100, ($box->total_weight_g / 3000) * 100);
        $weightBarClass = $weightPercent > 90 ? 'bg-danger' : ($weightPercent > 70 ? 'bg-plus' : 'bg-rausch');

        return view('boxes.customize', compact(
            'box',
            'availableItems',
            'availableBundles',
            'isLocked',
            'hoursUntilLock',
            'weightPercent',
            'weightBarClass'
        ));
    }

    public function swap(SwapItemRequest $request, Box $box): RedirectResponse
    {
        $box->load('subscription');
        abort_unless($request->user()->isAdmin() || $box->ownedBy($request->user()), Response::HTTP_FORBIDDEN);

        $outItem = BoxItem::query()->findOrFail($request->validated('remove_box_item_id'));
        $newItem = Item::query()->findOrFail($request->validated('new_item_id'));

        abort_unless($outItem->box_id === $box->id, Response::HTTP_FORBIDDEN);

        try {
            $result = $this->customizationService->swap(
                $box,
                $outItem,
                $newItem,
                $request->user(),
                (bool) ($request->validated('confirm_rotation') ?? false),
                (bool) ($request->validated('confirm_allergen') ?? false),
            );

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
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function remove(Request $request, Box $box, BoxItem $boxItem): RedirectResponse
    {
        $box->load('subscription');
        abort_unless($request->user()->isAdmin() || $box->ownedBy($request->user()), Response::HTTP_FORBIDDEN);
        abort_unless($boxItem->box_id === $box->id, Response::HTTP_FORBIDDEN);

        if ($box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast())) {
            return back()->with('error', 'Box is locked and cannot be modified.');
        }

        try {
            $this->customizationService->remove($box, $boxItem);

            return back()->with('success', 'Item removed successfully.');
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function add(AddBoxItemRequest $request, Box $box): RedirectResponse
    {
        $box->load('subscription');
        abort_unless($request->user()->isAdmin() || $box->ownedBy($request->user()), Response::HTTP_FORBIDDEN);

        $newItem = Item::query()->findOrFail($request->validated('new_item_id'));

        try {
            $result = $this->customizationService->add(
                $box,
                $newItem,
                $request->user(),
                (bool) ($request->validated('confirm_allergen') ?? false)
            );

            if ($result['status'] === 'warning') {
                return back()->with('add_warning', [
                    'type' => $result['type'],
                    'message' => $result['message'],
                    'new_item_id' => $newItem->id,
                    'new_item_name' => $newItem->name,
                ]);
            }

            return back()->with('success', $result['message']);
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function applyBundle(ApplyBundleToBoxRequest $request, Box $box): RedirectResponse
    {
        $box->load('subscription');
        abort_unless($request->user()->isAdmin() || $box->ownedBy($request->user()), Response::HTTP_FORBIDDEN);

        $bundle = Bundle::query()->where('is_active', true)->findOrFail($request->validated('bundle_id'));

        try {
            $itemCount = $this->bundleSelectorService->applyBundle($box, $bundle);

            return back()->with('success', "Bundle applied successfully with {$itemCount} items.");
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
