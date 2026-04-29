<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDriverRequest;
use App\Models\Driver;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\OperationsManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class DriverManagementController extends Controller
{
    public function __construct(
        private OperationsManagementService $operationsManagementService,
        private AuditLogService $auditLogService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'drivers' => Driver::query()->with('user')->latest()->get(),
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

        return back()->with('status', 'Driver active state updated.');
    }
}
