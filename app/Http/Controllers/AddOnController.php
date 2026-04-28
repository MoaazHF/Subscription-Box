<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddOnRequest;
use App\Models\Box;
use App\Services\AddOnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AddOnController extends Controller
{
    public function __construct(private readonly AddOnService $addOnService) {}

    /**
     * F16: List available add-on items for a box, excluding those already added.
     */
    public function index(Box $box): View
    {
        $existingItemIds = $box->boxItems()->pluck('item_id');
        $addOns = \App\Models\Item::where('is_addon', true)
            ->whereNotIn('id', $existingItemIds)
            ->with('allergenTags')
            ->get();

        return view('boxes.addons', compact('box', 'addOns'));
    }

    /**
     * F16: Add an add-on item to an open box.
     */
    public function store(AddOnRequest $request, Box $box): RedirectResponse
    {
        $item = \App\Models\Item::findOrFail($request->validated()['item_id']);
        $result = $this->addOnService->add($box, $item, $request->user());

        if (! $result['ok']) {
            return back()->withErrors(['addon' => $result['error']]);
        }

        return back()->with('success', 'Add-on added to your box.');
    }
}
