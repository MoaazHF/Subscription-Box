<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request): View
    {
        $driver = $request->user()->driver;

        abort_unless($driver && $driver->is_active, 403, 'You are not registered as an active driver.');

        $deliveries = Delivery::with(['address', 'box.subscription.user'])
            ->where('driver_id', $driver->id)
            ->whereIn('status', [Delivery::SHIPPED, Delivery::OUT_FOR_DELIVERY, Delivery::PICKING, Delivery::PACKED])
            ->orderBy('estimated_delivery', 'asc')
            ->get();

        return view('driver.index', compact('deliveries'));
    }

    public function updateStatus(Request $request, Delivery $delivery): RedirectResponse
    {
        $driver = $request->user()->driver;
        abort_unless($driver && $delivery->driver_id === $driver->id, 403);

        $request->validate([
            'status' => 'required|in:out_for_delivery,delivered,undeliverable',
        ]);

        $status = $request->string('status')->toString();

        $delivery->update([
            'status' => $status,
            'actual_delivery' => $status === Delivery::DELIVERED ? now() : null,
            'stops_remaining' => Delivery::STOPS_BY_STATUS[$status],
        ]);

        app(NotificationService::class)->notifyDeliveryStatus($delivery);

        return back()->with('success', 'Delivery status updated to '.ucfirst(str_replace('_', ' ', $status)));
    }
}
