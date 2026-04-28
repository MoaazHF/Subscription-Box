<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryStatusUpdateRequest;
use App\Models\Delivery;
use App\Notifications\DeliveryStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the authenticated user's deliveries.
     */
    public function index(): View
    {
        $deliveries = Delivery::forUser(Auth::id())
            ->with('address')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * Display the specified delivery.
     */
    public function show(Delivery $delivery): View
    {
        abort_unless(
            $delivery->address?->user_id === Auth::id(),
            403,
            'Unauthorized action.'
        );

        $delivery->load(['box', 'address', 'driver', 'claims']);

        return view('deliveries.show', compact('delivery'));
    }

    /**
     * Update the delivery status and/or delivery instructions.
     */
    public function update(DeliveryStatusUpdateRequest $request, Delivery $delivery): RedirectResponse
    {
        $previousStatus = $delivery->status;

        $delivery->fill($request->validated());

        if ($request->has('status') && $delivery->isDirty('status')) {
            $delivery->save();

            // Notify the subscriber via the database channel
            $delivery->address?->user?->notify(
                new DeliveryStatusChanged($delivery, $previousStatus)
            );
        } else {
            $delivery->save();
        }

        return redirect()
            ->route('deliveries.show', $delivery)
            ->with('success', 'Delivery updated successfully.');
    }
}
