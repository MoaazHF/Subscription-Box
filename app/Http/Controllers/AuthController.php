<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private AuditLogService $auditLogService
    ) {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials, remember: true)) {
            return back()->withErrors([
                'email' => 'The email or password is not correct.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $this->auditLogService->record(
            $request->user(),
            'auth.logged_in',
            $request->user(),
            [],
            $request->ip()
        );

        return redirect()->route('dashboard');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterUserRequest $request): RedirectResponse
    {
        $subscriberRoleId = Role::query()
            ->where('name', Role::SUBSCRIBER)
            ->value('id');

        $user = User::create([
            'role_id' => $subscriberRoleId,
            'name' => $request->validated('name'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $this->auditLogService->record($user, 'auth.registered', $user, [], $request->ip());

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();

        $this->auditLogService->record($user, 'auth.logged_out', $user, [], $request->ip());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
