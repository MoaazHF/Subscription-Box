<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWarehouseStaffAccountRequest;
use App\Http\Requests\StoreWarehouseStaffRequest;
use App\Http\Requests\UpdateWarehouseStaffProfileRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\WarehouseStaff;
use App\Services\AuditLogService;
use App\Services\OperationsManagementService;
use App\Services\WarehouseStaffAccountService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class WarehouseStaffController extends Controller
{
    public function __construct(
        private OperationsManagementService $operationsManagementService,
        private WarehouseStaffAccountService $warehouseStaffAccountService,
        private AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        $warehouseStaff = WarehouseStaff::query()
            ->with('user')
            ->latest('updated_at')
            ->get();

        $warehouseUsers = User::query()
            ->where('role_id', Role::query()->where('name', Role::WAREHOUSE_STAFF)->value('id'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('ops.warehouse-staff.index', [
            'warehouseStaff' => $warehouseStaff,
            'warehouseUsers' => $warehouseUsers,
        ]);
    }

    public function createAccount(CreateWarehouseStaffAccountRequest $request): RedirectResponse
    {
        $profile = $this->warehouseStaffAccountService->createAccountWithProfile($request->validated());

        $this->auditLogService->record($request->user(), 'warehouse_staff.account_created', $profile, [
            'user_id' => $profile->user_id,
            'email' => $profile->user?->email,
            'warehouse_location' => $profile->warehouse_location,
        ], $request->ip());

        return back()->with('status', 'Warehouse staff account created.');
    }

    public function store(StoreWarehouseStaffRequest $request): RedirectResponse
    {
        $user = User::query()->findOrFail($request->validated('user_id'));
        $this->operationsManagementService->assertUserRole($user, Role::WAREHOUSE_STAFF);

        $profile = $this->operationsManagementService->upsertWarehouseStaff($request->validated());

        $this->auditLogService->record($request->user(), 'warehouse_staff.upserted', $profile, $request->validated(), $request->ip());

        return back()->with('status', 'Warehouse staff profile saved.');
    }

    public function update(UpdateWarehouseStaffProfileRequest $request, WarehouseStaff $warehouseStaff): RedirectResponse
    {
        $profile = $this->warehouseStaffAccountService->updateProfile($warehouseStaff, $request->validated());

        $this->auditLogService->record($request->user(), 'warehouse_staff.profile_updated', $profile, $request->validated(), $request->ip());

        return back()->with('status', 'Warehouse staff profile updated.');
    }

    public function destroy(WarehouseStaff $warehouseStaff): RedirectResponse
    {
        $snapshot = $warehouseStaff->load('user')->toArray();
        $this->warehouseStaffAccountService->deleteProfile($warehouseStaff);

        $this->auditLogService->record(request()->user(), 'warehouse_staff.profile_deleted', $warehouseStaff, $snapshot, request()->ip());

        return back()->with('status', 'Warehouse staff profile deleted.');
    }
}
