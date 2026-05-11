@php($title = 'Bundles Control Panel')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <p class="air-kicker">Admin operations</p>
            <h1 class="air-title">Bundle Selector Control Panel</h1>
            <p class="air-copy">Define reusable item bundles and apply them from box customization.</p>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="air-panel space-y-4">
                <h2 class="air-title">Create bundle</h2>
                <form method="POST" action="{{ route('bundles.store') }}" class="space-y-3">
                    @csrf
                    <input name="name" type="text" class="air-input" placeholder="Bundle name" required>
                    <textarea name="description" class="air-input" placeholder="Description"></textarea>
                    <label class="inline-flex items-center gap-2 text-sm text-ink">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" checked>
                        Active bundle
                    </label>
                    <div class="space-y-2 max-h-64 overflow-auto border border-hairline rounded-2xl p-3">
                        @foreach ($items as $item)
                            <div class="grid grid-cols-[1fr_auto] gap-2">
                                <label class="inline-flex items-center gap-2 text-sm text-ink">
                                    <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch">
                                    {{ $item->name }} (stock: {{ $item->stock_qty }})
                                </label>
                                <input type="number" name="quantities[{{ $item->id }}]" value="1" min="1" max="25" class="air-input w-20">
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="air-button-primary w-full">Create bundle</button>
                </form>
            </div>

            <div class="air-panel space-y-4">
                <h2 class="air-title">Existing bundles</h2>
                @forelse ($bundles as $bundle)
                    <article class="air-grid-card space-y-3">
                        <div class="flex flex-wrap justify-between gap-2">
                            <p class="font-semibold text-ink">{{ $bundle->name }}</p>
                            <span class="air-chip">{{ $bundle->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <p class="text-sm text-ash">{{ $bundle->description }}</p>
                        <ul class="text-xs text-ash space-y-1">
                            @foreach ($bundle->bundleItems as $bundleItem)
                                <li>{{ $bundleItem->item?->name ?? 'Unknown item' }} × {{ $bundleItem->quantity }}</li>
                            @endforeach
                        </ul>

                        <form method="POST" action="{{ route('bundles.update', $bundle) }}" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input name="name" type="text" value="{{ $bundle->name }}" class="air-input" required>
                            <textarea name="description" class="air-input">{{ $bundle->description }}</textarea>
                            <label class="inline-flex items-center gap-2 text-sm text-ink">
                                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" @checked($bundle->is_active)>
                                Active bundle
                            </label>
                            <div class="space-y-2 max-h-48 overflow-auto border border-hairline rounded-2xl p-3">
                                @foreach ($items as $item)
                                    @php($selected = $bundle->bundleItems->firstWhere('item_id', $item->id))
                                    <div class="grid grid-cols-[1fr_auto] gap-2">
                                        <label class="inline-flex items-center gap-2 text-sm text-ink">
                                            <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" @checked($selected)>
                                            {{ $item->name }}
                                        </label>
                                        <input type="number" name="quantities[{{ $item->id }}]" value="{{ $selected?->quantity ?? 1 }}" min="1" max="25" class="air-input w-20">
                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="air-button-primary">Update bundle</button>
                        </form>

                        <form method="POST" action="{{ route('bundles.destroy', $bundle) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="air-button-danger">Delete bundle</button>
                        </form>
                    </article>
                @empty
                    <div class="air-panel-soft">
                        <p class="text-sm text-ash">No bundles created yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
