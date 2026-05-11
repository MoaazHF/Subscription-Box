<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Models\Claim;
use App\Models\Delivery;
use Illuminate\Http\RedirectResponse;

class ClaimController extends Controller
{
    public function store(StoreClaimRequest $request, Delivery $delivery): RedirectResponse
    {
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('claims', 'public');
        }

        Claim::query()->create([
            'subscription_id' => $delivery->box->subscription_id,
            'delivery_id' => $delivery->id,
            'item_id' => $request->string('item_id')->toString() ?: null,
            'type' => $request->string('type')->toString(),
            'description' => $request->string('description')->toString(),
            'photo_url' => $photoPath,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Your claim has been submitted successfully.');
    }
}
