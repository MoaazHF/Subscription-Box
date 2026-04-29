<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOwnPasswordRequest;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AccountSecurityController extends Controller
{
    public function __construct(
        private AuditLogService $auditLogService
    ) {}

    public function edit(): View
    {
        return view('auth.force-password-change');
    }

    public function update(UpdateOwnPasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
            'must_change_password' => false,
        ]);

        $this->auditLogService->record($request->user(), 'auth.password_changed_first_login', $request->user(), [], $request->ip());

        return redirect()->route('dashboard')->with('status', 'Password changed successfully.');
    }
}
