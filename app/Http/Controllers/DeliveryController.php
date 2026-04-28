<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDeliveryStatusRequest;
use App\Models\Delivery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeliveryController extends Controller
{
    public function index(Request $request): View
    {
        $deliveries = Delivery::query()
            ->with(['address', 'box.subscription.user'])
            ->when(
                ! $request->user()->isAdmin(),
                fn ($query) => $query->whereHas('address', fn ($addressQuery) => $addressQuery->whereBelongsTo($request->user()))
            )
            ->latest()
            ->get();

        return view('deliveries.index', [
            'deliveries' => $deliveries,
            'isAdminView' => $request->user()->isAdmin(),
        ]);
    }

    public function show(Request $request, Delivery $delivery): View
    {
        $this->ensureAccess($request, $delivery);

        $delivery->load(['box.subscription.user', 'address']);

        return view('deliveries.show', [
            'delivery' => $delivery,
        ]);
    }

    public function updateStatus(UpdateDeliveryStatusRequest $request, Delivery $delivery): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), Response::HTTP_FORBIDDEN);

        $payload = $request->validated();
        $status = $payload['status'];

        $delivery->update([
            'status' => $status,
            'tracking_number' => $payload['tracking_number'] ?? $delivery->tracking_number,
            'estimated_delivery' => $payload['estimated_delivery'] ?? $delivery->estimated_delivery,
            'delivery_instructions' => $payload['delivery_instructions'] ?? $delivery->delivery_instructions,
            'eco_dispatch' => (bool) ($payload['eco_dispatch'] ?? $delivery->eco_dispatch),
            'stops_remaining' => Delivery::STOPS_BY_STATUS[$status],
            'actual_delivery' => $status === Delivery::DELIVERED ? now() : null,
        ]);

        return redirect()
            ->route('deliveries.show', $delivery)
            ->with('status', 'Delivery status updated.');
    }

    private function ensureAccess(Request $request, Delivery $delivery): void
    {
        if ($request->user()->isAdmin()) {
            return;
        }

        abort_unless($delivery->belongsToUser($request->user()), Response::HTTP_FORBIDDEN);
    }
}
