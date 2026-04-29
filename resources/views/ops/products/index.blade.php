@php($title = 'Products Control Panel')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="air-kicker">Admin catalog</p>
                    <h1 class="air-title">Products Control Panel</h1>
                    <p class="air-copy">Create, update, and remove products used across subscription boxes with image-based catalog visibility.</p>
                </div>
                <span class="air-chip-dark">{{ $products->count() }} products</span>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">New product</p>
                    <h2 class="air-title">Add product to catalog.</h2>
                </div>

                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="grid gap-4">
                    @csrf
                    <input name="name" type="text" class="air-input" placeholder="Product name" required>
                    <textarea name="description" class="air-input min-h-28" placeholder="Description"></textarea>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <input name="weight_g" type="number" min="1" class="air-input" placeholder="Weight (g)" required>
                        <select name="size_category" class="air-select" required>
                            <option value="small">Small</option>
                            <option value="medium" selected>Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <input name="unit_price" type="number" min="0.01" step="0.01" class="air-input" placeholder="Unit price" required>
                        <input name="stock_qty" type="number" min="0" class="air-input" placeholder="Stock quantity" required>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <input name="supplier" type="text" class="air-input" placeholder="Supplier">
                        <input name="origin_country" type="text" maxlength="2" class="air-input uppercase" placeholder="Country code">
                    </div>

                    <textarea name="sourcing_notes" class="air-input min-h-24" placeholder="Sourcing notes"></textarea>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                            <input type="checkbox" name="is_limited_edition" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch">
                            Limited edition
                        </label>
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                            <input type="checkbox" name="is_addon" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch">
                            Available as addon
                        </label>
                    </div>

                    <input name="limited_stock" type="number" min="1" class="air-input" placeholder="Limited stock (required when limited edition)">

                    <div class="space-y-2">
                        <label for="image" class="text-sm font-semibold text-ink">Product image</label>
                        <input id="image" name="image" type="file" accept="image/png,image/jpeg,image/webp" class="air-input">
                    </div>

                    <button type="submit" class="air-button-primary w-full">Create product</button>
                </form>
            </div>

            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Catalog records</p>
                    <h2 class="air-title">Manage existing products.</h2>
                </div>

                <div class="space-y-5">
                    @forelse ($products as $product)
                        <article class="air-grid-card space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    @if ($product->image_url)
                                        <img src="{{ asset('storage/'.$product->image_url) }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-2xl border border-hairline object-cover">
                                    @else
                                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-hairline bg-cloud text-xs font-semibold text-ash">No image</div>
                                    @endif
                                    <div>
                                        <p class="text-lg font-semibold text-ink">{{ $product->name }}</p>
                                        <p class="text-sm text-ash">${{ number_format((float) $product->unit_price, 2) }} · {{ $product->stock_qty }} in stock</p>
                                    </div>
                                </div>
                                <span class="air-chip">{{ ucfirst($product->size_category) }}</span>
                            </div>

                            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="grid gap-3">
                                @csrf
                                @method('PUT')

                                <input name="name" type="text" class="air-input" value="{{ $product->name }}" required>
                                <textarea name="description" class="air-input min-h-24">{{ $product->description }}</textarea>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="weight_g" type="number" min="1" class="air-input" value="{{ $product->weight_g }}" required>
                                    <select name="size_category" class="air-select" required>
                                        <option value="small" @selected($product->size_category === 'small')>Small</option>
                                        <option value="medium" @selected($product->size_category === 'medium')>Medium</option>
                                        <option value="large" @selected($product->size_category === 'large')>Large</option>
                                    </select>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="unit_price" type="number" min="0.01" step="0.01" class="air-input" value="{{ number_format((float) $product->unit_price, 2, '.', '') }}" required>
                                    <input name="stock_qty" type="number" min="0" class="air-input" value="{{ $product->stock_qty }}" required>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="supplier" type="text" class="air-input" value="{{ $product->supplier }}" placeholder="Supplier">
                                    <input name="origin_country" type="text" maxlength="2" class="air-input uppercase" value="{{ $product->origin_country }}" placeholder="Country code">
                                </div>

                                <textarea name="sourcing_notes" class="air-input min-h-20" placeholder="Sourcing notes">{{ $product->sourcing_notes }}</textarea>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                        <input type="checkbox" name="is_limited_edition" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" @checked($product->is_limited_edition)>
                                        Limited edition
                                    </label>
                                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                        <input type="checkbox" name="is_addon" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" @checked($product->is_addon)>
                                        Available as addon
                                    </label>
                                </div>

                                <input name="limited_stock" type="number" min="1" class="air-input" value="{{ $product->limited_stock }}" placeholder="Limited stock">

                                <div class="grid gap-3 sm:grid-cols-2 sm:items-center">
                                    <input name="image" type="file" accept="image/png,image/jpeg,image/webp" class="air-input">
                                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                        <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch">
                                        Remove current image
                                    </label>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <button type="submit" class="air-button-primary">Update</button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('products.destroy', $product) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="air-button-danger">Delete</button>
                            </form>
                        </article>
                    @empty
                        <div class="air-panel-soft">
                            <p class="text-sm text-ash">No products in catalog yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
