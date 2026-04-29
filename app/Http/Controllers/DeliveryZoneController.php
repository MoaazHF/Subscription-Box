<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryZoneRequest;
use App\Models\DeliveryZone;
use App\Services\AuditLogService;
use App\Services\OperationsManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DeliveryZoneController extends Controller
{
    public function __construct(
        private OperationsManagementService $operationsManagementService,
        private AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        return view('ops.delivery-zones.index', [
            'zones' => DeliveryZone::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreDeliveryZoneRequest $request): RedirectResponse
    {
        $zone = $this->operationsManagementService->createDeliveryZone($request->validated());

        $this->auditLogService->record($request->user(), 'delivery_zone.created', $zone, $request->validated(), $request->ip());

        return back()->with('status', 'Delivery zone created.');
    }

    public function toggleServiceability(DeliveryZone $deliveryZone): RedirectResponse
    {
        $zone = $this->operationsManagementService->toggleZoneServiceability($deliveryZone);

        return back()->with('status', "Delivery zone {$zone->name} updated.");
    }
}
