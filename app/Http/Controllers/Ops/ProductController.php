<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Item;
use App\Services\AuditLogService;
use App\Services\ProductCatalogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductCatalogService $productCatalogService,
        private AuditLogService $auditLogService
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $sizeCategory = trim((string) $request->string('size_category'));
        $stockState = trim((string) $request->string('stock_state'));
        $isAddon = $request->string('is_addon')->toString();
        $isLimitedEdition = $request->string('is_limited_edition')->toString();

        $products = Item::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('supplier', 'like', "%{$search}%")
                    ->orWhere('origin_country', 'like', "%{$search}%");
            })
            ->when($sizeCategory !== '', fn ($query) => $query->where('size_category', $sizeCategory))
            ->when($isAddon !== '', fn ($query) => $query->where('is_addon', $isAddon === '1'))
            ->when($isLimitedEdition !== '', fn ($query) => $query->where('is_limited_edition', $isLimitedEdition === '1'))
            ->when($stockState === 'in_stock', fn ($query) => $query->where('stock_qty', '>', 0))
            ->when($stockState === 'out_of_stock', fn ($query) => $query->where('stock_qty', '<=', 0))
            ->latest('updated_at')
            ->get();

        return view('ops.products.index', [
            'products' => $products,
            'filters' => [
                'q' => $search,
                'size_category' => $sizeCategory,
                'stock_state' => $stockState,
                'is_addon' => $isAddon,
                'is_limited_edition' => $isLimitedEdition,
            ],
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $product = $this->productCatalogService->create($validated);

        $auditMetadata = collect($validated)
            ->except(['image'])
            ->all();

        $auditMetadata['image_uploaded'] = $request->hasFile('image');

        $this->auditLogService->record($request->user(), 'product.created', $product, $auditMetadata, $request->ip());

        return back()->with('status', 'Product created successfully.');
    }

    public function update(UpdateProductRequest $request, Item $product): RedirectResponse
    {
        $validated = $request->validated();
        $updatedProduct = $this->productCatalogService->update($product, $validated);

        $auditMetadata = collect($validated)
            ->except(['image'])
            ->all();

        $auditMetadata['image_uploaded'] = $request->hasFile('image');

        $this->auditLogService->record($request->user(), 'product.updated', $updatedProduct, $auditMetadata, $request->ip());

        return back()->with('status', 'Product updated successfully.');
    }

    public function destroy(Item $product): RedirectResponse
    {
        $snapshot = $product->toArray();
        $this->productCatalogService->delete($product);

        $this->auditLogService->record(request()->user(), 'product.deleted', $product, $snapshot, request()->ip());

        return back()->with('status', 'Product deleted successfully.');
    }
}
