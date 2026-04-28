<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Models\Claim;
use App\Models\Delivery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClaimController extends Controller
{
    /**
     * Show the form for creating a new claim against a delivery.
     */
    public function create(Delivery $delivery): View
    {
        abort_unless(
            $delivery->address?->user_id === Auth::id(),
            403,
            'Unauthorized action.'
        );

        return view('claims.create', compact('delivery'));
    }

    /**
     * Store a newly created claim.
     */
    public function store(StoreClaimRequest $request, Delivery $delivery): RedirectResponse
    {
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('claims/photos', 'public');
        }

        $claim = $delivery->claims()->create([
            'type' => $request->validated('type'),
            'description' => $request->validated('description'),
            'photo_path' => $photoPath,
            'status' => 'open',
        ]);

        return redirect()
            ->route('claims.show', $claim)
            ->with('success', 'Your claim has been submitted successfully.');
    }

    /**
     * Display the specified claim.
     */
    public function show(Claim $claim): View
    {
        $claim->load('delivery.address');

        abort_unless(
            $claim->delivery?->address?->user_id === Auth::id(),
            403,
            'Unauthorized action.'
        );

        return view('claims.show', compact('claim'));
    }
}
