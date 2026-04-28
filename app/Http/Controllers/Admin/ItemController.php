<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Models\AllergenTag;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    /** Display a listing of all items. */
    public function index(): View
    {
        $items = Item::with('allergenTags')->orderBy('name')->paginate(20);

        return view('admin.items.index', compact('items'));
    }

    /** Show the form for creating a new item. */
    public function create(): View
    {
        $allergenTags = AllergenTag::orderBy('name')->get();

        return view('admin.items.create', compact('allergenTags'));
    }

    /** Store a newly created item in the database. */
    public function store(StoreItemRequest $request): RedirectResponse
    {
        $item = Item::create($request->validated());
        $item->allergenTags()->sync($request->input('allergen_tag_ids', []));

        return redirect()->route('admin.items.index')->with('success', 'Item created successfully.');
    }

    /** Show the form for editing the specified item. */
    public function edit(Item $item): View
    {
        $allergenTags = AllergenTag::orderBy('name')->get();
        $item->load('allergenTags');

        return view('admin.items.edit', compact('item', 'allergenTags'));
    }

    /** Update the specified item in the database. */
    public function update(StoreItemRequest $request, Item $item): RedirectResponse
    {
        $item->update($request->validated());
        $item->allergenTags()->sync($request->input('allergen_tag_ids', []));

        return redirect()->route('admin.items.index')->with('success', 'Item updated successfully.');
    }

    /** Remove the specified item from the database. */
    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'Item deleted successfully.');
    }
}
