<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * F21: Display the subscriber item catalogue with allergen badges.
     */
    public function index(): View
    {
        $items = Item::with('allergenTags')->orderBy('name')->get();

        return view('items.index', compact('items'));
    }

    /**
     * F21: Display sourcing information for a single item.
     */
    public function show(Item $item): View
    {
        $item->load('allergenTags');

        return view('items.sourcing', compact('item'));
    }
}
