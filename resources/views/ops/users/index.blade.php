@php($title = 'Users Control Panel')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="air-kicker">Admin operations</p>
                    <h1 class="air-title">Users Control Panel</h1>
                    <p class="air-copy">Create, edit, and remove platform users from one admin panel.</p>
                </div>
                <span class="air-chip-dark">{{ $users->count() }} users</span>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Create user</p>
                    <h2 class="air-title">Add a new account.</h2>
                </div>

                <form method="POST" action="{{ route('admin-users.store') }}" class="grid gap-4">
                    @csrf
                    <input name="name" type="text" class="air-input" placeholder="Full name" required>
                    <input name="email" type="email" class="air-input" placeholder="Email address" required>
                    <input name="phone" type="text" class="air-input" placeholder="Phone number">

                    <div class="space-y-2">
                        <label for="role_id" class="text-sm font-semibold text-ink">Role</label>
                        <select id="role_id" name="role_id" class="air-select" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <input name="password" type="password" class="air-input" placeholder="Password" required>
                        <input name="password_confirmation" type="password" class="air-input" placeholder="Confirm password" required>
                    </div>

                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                        <input type="checkbox" name="must_change_password" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch">
                        Force password change on first login
                    </label>

                    <button type="submit" class="air-button-primary w-full">Create user</button>
                </form>
            </div>

            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Manage users</p>
                    <h2 class="air-title">Update or delete existing users.</h2>
                </div>

                <div class="space-y-4">
                    @forelse ($users as $user)
                        <article class="air-grid-card space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-lg font-semibold text-ink">{{ $user->name }}</p>
                                    <p class="text-sm text-ash">{{ $user->email }} @if ($user->phone) · {{ $user->phone }} @endif</p>
                                </div>
                                <span class="air-chip">{{ ucfirst(str_replace('_', ' ', $user->role?->name ?? 'unknown')) }}</span>
                            </div>

                            <form method="POST" action="{{ route('admin-users.update', $user) }}" class="grid gap-3">
                                @csrf
                                @method('PATCH')

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="name" type="text" class="air-input" value="{{ $user->name }}" required>
                                    <input name="email" type="email" class="air-input" value="{{ $user->email }}" required>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="phone" type="text" class="air-input" value="{{ $user->phone }}" placeholder="Phone number">
                                    <select name="role_id" class="air-select" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="password" type="password" class="air-input" placeholder="New password (optional)">
                                    <input name="password_confirmation" type="password" class="air-input" placeholder="Confirm new password">
                                </div>

                                <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                    <input type="checkbox" name="must_change_password" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" @checked($user->must_change_password)>
                                    Force password change on next login
                                </label>

                                <button type="submit" class="air-button-primary">Update user</button>
                            </form>

                            <form method="POST" action="{{ route('admin-users.destroy', $user) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="air-button-danger"
                                    @disabled(auth()->id() === $user->id)
                                >
                                    {{ auth()->id() === $user->id ? 'Cannot delete current account' : 'Delete user' }}
                                </button>
                            </form>
                        </article>
                    @empty
                        <div class="air-panel-soft">
                            <p class="text-sm text-ash">No users available.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
