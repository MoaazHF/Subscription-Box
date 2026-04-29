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

class ProductController extends Controller
{
    public function __construct(
        private ProductCatalogService $productCatalogService,
        private AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        $products = Item::query()
            ->latest('updated_at')
            ->get();

        return view('ops.products.index', [
            'products' => $products,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productCatalogService->create($request->validated());

        $this->auditLogService->record($request->user(), 'product.created', $product, $request->validated(), $request->ip());

        return back()->with('status', 'Product created successfully.');
    }

    public function update(UpdateProductRequest $request, Item $product): RedirectResponse
    {
        $updatedProduct = $this->productCatalogService->update($product, $request->validated());

        $this->auditLogService->record($request->user(), 'product.updated', $updatedProduct, $request->validated(), $request->ip());

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
