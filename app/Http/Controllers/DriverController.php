<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDriverDeliveryStatusRequest;
use App\Models\Delivery;
use App\Services\DeliveryStateTransitionService;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct(private DeliveryStateTransitionService $deliveryStateTransitionService) {}

    public function index(Request $request): View
    {
        $driver = $request->user()->driver;

        abort_unless($driver && $driver->is_active, 403, 'You are not registered as an active driver.');

        $deliveries = Delivery::query()
            ->with(['address', 'box.subscription.user'])
            ->where('driver_id', $driver->id)
            ->whereIn('status', [
                Delivery::PENDING,
                Delivery::PICKING,
                Delivery::PACKED,
                Delivery::SHIPPED,
                Delivery::OUT_FOR_DELIVERY,
            ])
            ->orderBy('estimated_delivery', 'asc')
            ->get();

        return view('driver.index', compact('deliveries'));
    }

    public function updateStatus(UpdateDriverDeliveryStatusRequest $request, Delivery $delivery): RedirectResponse
    {
        $driver = $request->user()->driver;
        abort_unless($driver && $delivery->driver_id === $driver->id, 403);

        $status = $request->validated('status');

        $this->deliveryStateTransitionService->apply($delivery, $status, [], true);

        app(NotificationService::class)->notifyDeliveryStatus($delivery);

        return back()->with('success', 'Delivery status updated to '.ucfirst(str_replace('_', ' ', $status)));
    }
}
