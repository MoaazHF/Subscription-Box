<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarehouseStaffRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\WarehouseStaff;
use App\Services\AuditLogService;
use App\Services\OperationsManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class WarehouseStaffController extends Controller
{
    public function __construct(
        private OperationsManagementService $operationsManagementService,
        private AuditLogService $auditLogService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'warehouse_staff' => WarehouseStaff::query()->with('user')->latest()->get(),
        ]);
    }

    public function store(StoreWarehouseStaffRequest $request): RedirectResponse
    {
        $user = User::query()->findOrFail($request->validated('user_id'));
        $this->operationsManagementService->assertUserRole($user, Role::WAREHOUSE_STAFF);

        $profile = $this->operationsManagementService->upsertWarehouseStaff($request->validated());

        $this->auditLogService->record($request->user(), 'warehouse_staff.upserted', $profile, $request->validated(), $request->ip());

        return back()->with('status', 'Warehouse staff profile saved.');
    }
}
