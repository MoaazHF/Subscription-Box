<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Driver;
use App\Notifications\DeliveryStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DriverController extends Controller
{
    /**
     * Display a listing of deliveries assigned to the currently authenticated driver.
     */
    public function index(Request $request): View
    {
        // Resolve the driver record tied to this user session (by driver_id stored in session or user model).
        // For now we demonstrate: pass ?driver_id= query param (swap for auth driver guard later).
        $driverId = $request->query('driver_id');

        $driver = Driver::findOrFail($driverId);

        $deliveries = Delivery::where('driver_id', $driver->id)
            ->with(['address', 'box'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('driver.index', compact('driver', 'deliveries'));
    }

    /**
     * Display a single delivery assigned to this driver.
     */
    public function show(Request $request, Delivery $delivery): View
    {
        abort_unless(
            $delivery->driver_id === $request->query('driver_id'),
            403,
            'You are not assigned to this delivery.'
        );

        $delivery->load(['address', 'box']);

        return view('driver.show', compact('delivery'));
    }

    /**
     * Allow a driver to update the status of their assigned delivery.
     */
    public function update(Request $request, Delivery $delivery): RedirectResponse
    {
        abort_unless(
            $delivery->driver_id === $request->input('driver_id'),
            403,
            'You are not assigned to this delivery.'
        );

        $allowedNext = Delivery::STATUS_TRANSITIONS[$delivery->status] ?? [];

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in($allowedNext)],
        ]);

        $previousStatus = $delivery->status;
        $delivery->update(['status' => $validated['status']]);

        // Notify the subscriber
        $delivery->address?->user?->notify(
            new DeliveryStatusChanged($delivery, $previousStatus)
        );

        return redirect()
            ->route('driver.show', ['delivery' => $delivery, 'driver_id' => $request->input('driver_id')])
            ->with('success', 'Status updated to: '.ucfirst(str_replace('_', ' ', $delivery->status)));
    }
}
