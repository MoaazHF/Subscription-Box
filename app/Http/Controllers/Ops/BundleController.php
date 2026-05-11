<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBundleRequest;
use App\Http\Requests\UpdateBundleRequest;
use App\Models\Bundle;
use App\Models\BundleItem;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BundleController extends Controller
{
    public function index(): View
    {
        return view('ops.bundles.index', [
            'bundles' => Bundle::query()->with(['bundleItems.item'])->orderBy('name')->get(),
            'items' => Item::query()->orderBy('name')->get(['id', 'name', 'stock_qty']),
        ]);
    }

    public function store(StoreBundleRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        DB::transaction(function () use ($payload): void {
            $bundle = Bundle::query()->create([
                'name' => $payload['name'],
                'description' => $payload['description'] ?? null,
                'is_active' => (bool) ($payload['is_active'] ?? false),
            ]);

            foreach ($payload['item_ids'] as $itemId) {
                BundleItem::query()->create([
                    'bundle_id' => $bundle->id,
                    'item_id' => $itemId,
                    'quantity' => (int) ($payload['quantities'][$itemId] ?? 1),
                ]);
            }
        });

        return back()->with('status', 'Bundle created.');
    }

    public function update(UpdateBundleRequest $request, Bundle $bundle): RedirectResponse
    {
        $payload = $request->validated();

        DB::transaction(function () use ($bundle, $payload): void {
            $bundle->update([
                'name' => $payload['name'],
                'description' => $payload['description'] ?? null,
                'is_active' => (bool) ($payload['is_active'] ?? false),
            ]);

            $bundle->bundleItems()->delete();

            foreach ($payload['item_ids'] as $itemId) {
                BundleItem::query()->create([
                    'bundle_id' => $bundle->id,
                    'item_id' => $itemId,
                    'quantity' => (int) ($payload['quantities'][$itemId] ?? 1),
                ]);
            }
        });

        return back()->with('status', 'Bundle updated.');
    }

    public function destroy(Bundle $bundle): RedirectResponse
    {
        $bundle->delete();

        return back()->with('status', 'Bundle deleted.');
    }
}
