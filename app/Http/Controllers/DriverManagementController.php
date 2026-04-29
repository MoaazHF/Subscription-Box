<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignDriverDeliveryRequest;
use App\Http\Requests\StoreDriverRequest;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\OperationsManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DriverManagementController extends Controller
{
    public function __construct(
        private OperationsManagementService $operationsManagementService,
        private AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        $drivers = Driver::query()
            ->with(['user', 'deliveries' => fn ($query) => $query->latest('updated_at')])
            ->withCount([
                'deliveries as deliveries_total_count',
                'deliveries as deliveries_pending_count' => fn ($query) => $query->where('status', Delivery::PENDING),
                'deliveries as deliveries_out_count' => fn ($query) => $query->where('status', Delivery::OUT_FOR_DELIVERY),
                'deliveries as deliveries_delivered_count' => fn ($query) => $query->where('status', Delivery::DELIVERED),
            ])
            ->latest('updated_at')
            ->get();

        $driverUsers = User::query()
            ->where('role_id', Role::query()->where('name', Role::DRIVER)->value('id'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $assignableDeliveries = Delivery::query()
            ->with(['address', 'box.subscription.user'])
            ->whereNotIn('status', [Delivery::DELIVERED, Delivery::UNDELIVERABLE])
            ->orderByDesc('created_at')
            ->get();

        return view('ops.drivers.index', [
            'drivers' => $drivers,
            'driverUsers' => $driverUsers,
            'assignableDeliveries' => $assignableDeliveries,
        ]);
    }

    public function store(StoreDriverRequest $request): RedirectResponse
    {
        $user = User::query()->findOrFail($request->validated('user_id'));
        $this->operationsManagementService->assertUserRole($user, Role::DRIVER);

        $driver = $this->operationsManagementService->upsertDriver($request->validated());

        $this->auditLogService->record($request->user(), 'driver.upserted', $driver, $request->validated(), $request->ip());

        return back()->with('status', 'Driver profile saved.');
    }

    public function toggle(Driver $driver): RedirectResponse
    {
        $driver->update(['is_active' => ! $driver->is_active]);

        $this->auditLogService->record(request()->user(), 'driver.toggled_active', $driver, [
            'is_active' => $driver->is_active,
        ], request()->ip());

        return back()->with('status', 'Driver active state updated.');
    }

    public function assignDelivery(AssignDriverDeliveryRequest $request, Driver $driver): RedirectResponse
    {
        $delivery = Delivery::query()->findOrFail($request->validated('delivery_id'));

        $assignedDelivery = $this->operationsManagementService->assignDelivery($driver, $delivery);

        $this->auditLogService->record(request()->user(), 'delivery.assigned_driver', $assignedDelivery, [
            'driver_id' => $driver->id,
        ], request()->ip());

        return back()->with('status', 'Delivery assigned to driver successfully.');
    }
}
