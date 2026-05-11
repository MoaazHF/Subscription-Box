<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDeliveryStatusRequest;
use App\Models\Delivery;
use App\Services\DeliveryStateTransitionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeliveryController extends Controller
{
    public function __construct(private DeliveryStateTransitionService $deliveryStateTransitionService) {}

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

        $delivery->load(['box.subscription.user', 'address', 'claims.resolvedBy']);

        return view('deliveries.show', [
            'delivery' => $delivery,
        ]);
    }

    public function updateStatus(UpdateDeliveryStatusRequest $request, Delivery $delivery): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), Response::HTTP_FORBIDDEN);

        $payload = $request->validated();

        $this->deliveryStateTransitionService->apply($delivery, $payload['status'], $payload);

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
