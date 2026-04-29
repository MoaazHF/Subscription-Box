@php($title = 'Warehouse Staff Control Panel')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="air-kicker">Admin operations</p>
                    <h1 class="air-title">Warehouse Staff Control Panel</h1>
                    <p class="air-copy">Register warehouse staff profiles and monitor assigned locations for fulfillment operations.</p>
                </div>
                <span class="air-chip-dark">{{ $warehouseStaff->count() }} profiles</span>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Profile setup</p>
                    <h2 class="air-title">Create or update staff profile.</h2>
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

                    <button type="submit" class="air-button-primary w-full">Save warehouse profile</button>
                </form>
            </div>

            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Current staff</p>
                    <h2 class="air-title">Warehouse team status.</h2>
                </div>

                <div class="space-y-4">
                    @forelse ($warehouseStaff as $staff)
                        <article class="air-grid-card">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-lg font-semibold text-ink">{{ $staff->user?->name ?? 'Unknown user' }}</p>
                                    <p class="text-sm text-ash">{{ $staff->user?->email ?? 'No email' }}</p>
                                </div>
                                <span class="air-chip">Warehouse Staff</span>
                            </div>

                            <div class="mt-4 air-stat">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Assigned location</p>
                                <p class="mt-2 text-sm font-semibold text-ink">{{ $staff->warehouse_location ?? 'Not assigned' }}</p>
                            </div>
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
