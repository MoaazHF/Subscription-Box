<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreManagedUserRequest;
use App\Http\Requests\UpdateManagedUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct(
        private AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        return view('ops.users.index', [
            'users' => User::query()
                ->with('role')
                ->latest('created_at')
                ->get(['id', 'role_id', 'name', 'phone', 'email', 'must_change_password', 'created_at']),
            'roles' => Role::query()->orderBy('id')->get(['id', 'name']),
        ]);
    }

    public function store(StoreManagedUserRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        $user = User::query()->create([
            'role_id' => (int) $payload['role_id'],
            'name' => $payload['name'],
            'phone' => $payload['phone'] ?? null,
            'email' => $payload['email'],
            'password' => $payload['password'],
            'must_change_password' => (bool) ($payload['must_change_password'] ?? false),
        ]);

        $this->auditLogService->record($request->user(), 'user.admin_created', $user, [
            'role_id' => $user->role_id,
            'must_change_password' => $user->must_change_password,
        ], $request->ip());

        return back()->with('status', 'User account created.');
    }

    public function update(UpdateManagedUserRequest $request, User $user): RedirectResponse
    {
        $payload = $request->validated();
        $adminRoleId = $this->adminRoleId();

        if ((int) $payload['role_id'] !== $adminRoleId && $user->role_id === $adminRoleId && $this->adminUsersCount() <= 1) {
            return back()->with('error', 'At least one admin user must remain.');
        }

        $updatePayload = [
            'role_id' => (int) $payload['role_id'],
            'name' => $payload['name'],
            'phone' => $payload['phone'] ?? null,
            'email' => $payload['email'],
            'must_change_password' => (bool) ($payload['must_change_password'] ?? false),
        ];

        if (! empty($payload['password'])) {
            $updatePayload['password'] = $payload['password'];
        }

        $user->update($updatePayload);

        $this->auditLogService->record($request->user(), 'user.admin_updated', $user, [
            'role_id' => $user->role_id,
            'must_change_password' => $user->must_change_password,
        ], $request->ip());

        return back()->with('status', 'User account updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->is($user)) {
            return back()->with('error', 'You cannot delete your current account.');
        }

        if ($user->role_id === $this->adminRoleId() && $this->adminUsersCount() <= 1) {
            return back()->with('error', 'At least one admin user must remain.');
        }

        $snapshot = $user->load('role')->toArray();
        $user->delete();

        $this->auditLogService->record($request->user(), 'user.admin_deleted', $user, $snapshot, $request->ip());

        return back()->with('status', 'User account deleted.');
    }

    private function adminRoleId(): int
    {
        return (int) Role::query()
            ->where('name', Role::ADMIN)
            ->value('id');
    }

    private function adminUsersCount(): int
    {
        return User::query()
            ->where('role_id', $this->adminRoleId())
            ->count();
    }
}
