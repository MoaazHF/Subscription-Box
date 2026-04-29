@php($title = 'Warehouse Staff Control Panel')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="air-kicker">Admin operations</p>
                    <h1 class="air-title">Warehouse Staff Control Panel</h1>
                    <p class="air-copy">Create warehouse staff accounts, assign locations, and maintain profile records from one board.</p>
                </div>
                <span class="air-chip-dark">{{ $warehouseStaff->count() }} profiles</span>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="space-y-6">
                <div class="air-panel space-y-6">
                    <div>
                        <p class="air-kicker">Account creation</p>
                        <h2 class="air-title">Create warehouse staff login.</h2>
                    </div>

                    <form method="POST" action="{{ route('warehouse-staff.accounts.store') }}" class="grid gap-4">
                        @csrf
                        <input name="name" type="text" class="air-input" placeholder="Full name" required>
                        <input name="email" type="email" class="air-input" placeholder="Email" required>
                        <input name="phone" type="text" class="air-input" placeholder="Phone">
                        <input name="warehouse_location" type="text" class="air-input" placeholder="Warehouse location">

                        <div class="grid gap-3 sm:grid-cols-2">
                            <input name="password" type="password" class="air-input" placeholder="Initial password" required>
                            <input name="password_confirmation" type="password" class="air-input" placeholder="Confirm password" required>
                        </div>

                        <button type="submit" class="air-button-primary w-full">Create warehouse account</button>
                    </form>
                </div>

                <div class="air-panel space-y-6">
                    <div>
                        <p class="air-kicker">Profile setup</p>
                        <h2 class="air-title">Attach location to existing warehouse user.</h2>
                    </div>

                    <form method="POST" action="{{ route('warehouse-staff.store') }}" class="grid gap-4">
                        @csrf
                        <div class="space-y-2">
                            <label for="user_id" class="text-sm font-semibold text-ink">Warehouse user</label>
                            <select id="user_id" name="user_id" class="air-select">
                                @foreach ($warehouseUsers as $warehouseUser)
                                    <option value="{{ $warehouseUser->id }}">{{ $warehouseUser->name }} ({{ $warehouseUser->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="warehouse_location" class="text-sm font-semibold text-ink">Warehouse location</label>
                            <input id="warehouse_location" name="warehouse_location" type="text" class="air-input" placeholder="Main Distribution Center">
                        </div>

                        <button type="submit" class="air-button-secondary w-full">Save profile only</button>
                    </form>
                </div>
            </div>

            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Current staff</p>
                    <h2 class="air-title">Warehouse team status.</h2>
                </div>

                <div class="space-y-4">
                    @forelse ($warehouseStaff as $staff)
                        <article class="air-grid-card space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-lg font-semibold text-ink">{{ $staff->user?->name ?? 'Unknown user' }}</p>
                                    <p class="text-sm text-ash">{{ $staff->user?->email ?? 'No email' }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="air-chip">Warehouse Staff</span>
                                    @if ($staff->user?->must_change_password)
                                        <span class="air-chip-dark">Password reset required</span>
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('warehouse-staff.update', $staff) }}" class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-ink">Warehouse location</label>
                                    <input name="warehouse_location" type="text" value="{{ $staff->warehouse_location }}" class="air-input" placeholder="Main Distribution Center">
                                </div>
                                <button type="submit" class="air-button-primary">Update</button>
                            </form>

                            <form method="POST" action="{{ route('warehouse-staff.destroy', $staff) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="air-button-danger">Delete profile</button>
                            </form>
                        </article>
                    @empty
                        <div class="air-panel-soft">
                            <p class="text-sm text-ash">No warehouse staff profiles available yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
