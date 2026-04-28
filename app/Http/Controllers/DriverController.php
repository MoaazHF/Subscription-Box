<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Show the deliveries assigned to the authenticated driver.
     */
    public function index(Request $request): View
    {
        $driver = $request->user()->driver;

        abort_unless($driver, 403, 'You are not registered as a driver.');

        $deliveries = Delivery::with(['address', 'box.subscription.user'])
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['shipped', 'out_for_delivery'])
            ->orderBy('estimated_delivery', 'asc')
            ->get();

        return view('driver.index', compact('deliveries'));
    }

    /**
     * Update the status of a specific delivery.
     */
    public function updateStatus(Request $request, Delivery $delivery): RedirectResponse
    {
        $driver = $request->user()->driver;
        abort_unless($driver && $delivery->driver_id === $driver->id, 403);

        $request->validate([
            'status' => 'required|in:out_for_delivery,delivered,failed',
        ]);

        $delivery->status = $request->status;

        if ($request->status === 'delivered') {
            $delivery->actual_delivery = now();
        }

        $delivery->save();

        // Optional: Trigger a notification here.
        if (class_exists(NotificationService::class)) {
            app(NotificationService::class)->notifyDeliveryStatus($delivery);
        }

        return back()->with('success', 'Delivery status updated to '.ucfirst(str_replace('_', ' ', $request->status)));
    }
}
