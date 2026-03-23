<?php

class Auth
{
    public static function user(): ?array
    {
        return Session::get('auth_user');
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function login(array $user): void
    {
        Session::put('auth_user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);
    }

    public static function logout(): void
    {
        Session::forget('auth_user');
    }

    public static function requireLogin(): void
    {
        if (! self::check()) {
            Session::flash('error', 'Please login first.');
            redirect('login');
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();

        if ((self::user()['role'] ?? '') !== $role) {
            Session::flash('error', 'You are not allowed to access this page.');
            redirect('dashboard');
        }
    }
}

