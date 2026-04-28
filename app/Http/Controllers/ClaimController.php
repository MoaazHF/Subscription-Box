<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    public function store(Request $request, Delivery $delivery)
    {
        $request->validate([
            'type' => 'required|in:damage,missing',
            'item_id' => 'nullable|exists:items,id',
            'description' => 'required|string|max:1000',
            'photo' => 'nullable|image|max:5120',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('claims', 'public');
            $photoUrl = Storage::url($path);
        }

        Claim::create([
            'subscription_id' => $delivery->box->subscription_id,
            'delivery_id' => $delivery->id,
            'item_id' => $request->item_id,
            'type' => $request->type,
            'description' => $request->description,
            'photo_url' => $photoUrl,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Your claim has been submitted successfully.');
    }
}
