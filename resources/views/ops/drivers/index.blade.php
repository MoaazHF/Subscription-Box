@php($title = 'Driver Control Panel')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="air-kicker">Admin operations</p>
                    <h1 class="air-title">Driver Operations Control Panel</h1>
                    <p class="air-copy">Activate or deactivate drivers, assign delivery orders, and follow delivery status per driver.</p>
                </div>
                <span class="air-chip-dark">{{ $drivers->count() }} drivers</span>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Driver profile</p>
                    <h2 class="air-title">Register or update a driver profile.</h2>
                </div>

                <form method="POST" action="{{ route('drivers.store') }}" class="grid gap-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="user_id" class="text-sm font-semibold text-ink">Driver user</label>
                        <select id="user_id" name="user_id" class="air-select">
                            @foreach ($driverUsers as $driverUser)
                                <option value="{{ $driverUser->id }}">{{ $driverUser->name }} ({{ $driverUser->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="vehicle_number" class="text-sm font-semibold text-ink">Vehicle number</label>
                        <input id="vehicle_number" name="vehicle_number" type="text" class="air-input" placeholder="DRV-1001">
                    </div>

                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" checked>
                        Active driver
                    </label>

                    <button type="submit" class="air-button-primary w-full">Save driver profile</button>
                </form>
            </div>

            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Order assignment</p>
                    <h2 class="air-title">Assign deliveries to a selected driver.</h2>
                    <p class="air-copy">Only deliveries that are not completed or undeliverable appear in the assignment list.</p>
                </div>

                @if ($assignableDeliveries->isEmpty())
                    <div class="air-panel-soft">
                        <p class="text-sm text-ash">No deliveries are currently available for assignment.</p>
                    </div>
                @endif

                <div class="space-y-4">
                    @foreach ($drivers as $driver)
                        <article class="air-grid-card space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-lg font-semibold text-ink">{{ $driver->user?->name ?? 'Unknown driver' }}</p>
                                    <p class="text-sm text-ash">{{ $driver->user?->email ?? 'No email' }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="{{ $driver->is_active ? 'air-chip-dark' : 'air-chip' }}">{{ $driver->is_active ? 'Active' : 'Inactive' }}</span>
                                    <form method="POST" action="{{ route('drivers.toggle', $driver) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="air-button-secondary">{{ $driver->is_active ? 'Deactivate' : 'Activate' }}</button>
                                    </form>
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-4">
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Vehicle</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $driver->vehicle_number ?? 'N/A' }}</p>
                                </div>
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Total</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $driver->deliveries_total_count }}</p>
                                </div>
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Pending</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $driver->deliveries_pending_count }}</p>
                                </div>
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Delivered</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $driver->deliveries_delivered_count }}</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('drivers.assign-delivery', $driver) }}" class="grid gap-3 sm:grid-cols-[1fr_auto]">
                                @csrf
                                @method('PATCH')
                                <select name="delivery_id" class="air-select" @disabled(! $driver->is_active || $assignableDeliveries->isEmpty())>
                                    @foreach ($assignableDeliveries as $delivery)
                                        <option value="{{ $delivery->id }}">
                                            {{ $delivery->tracking_number ?? 'No tracking' }}
                                            · {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                            · {{ $delivery->address?->city ?? 'Unknown city' }}
                                            · {{ $delivery->box?->subscription?->user?->email ?? 'Unknown subscriber' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="air-button-primary" @disabled(! $driver->is_active || $assignableDeliveries->isEmpty())>Assign Delivery</button>
                            </form>

                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Recent assigned deliveries</p>
                                @forelse ($driver->deliveries->take(5) as $delivery)
                                    <div class="rounded-[18px] border border-hairline bg-cloud px-4 py-3 text-sm">
                                        <span class="font-semibold text-ink">{{ $delivery->tracking_number ?? 'No tracking' }}</span>
                                        <span class="text-ash">· {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</span>
                                        <span class="text-ash">· {{ $delivery->updated_at?->format('M d, Y H:i') }}</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-ash">No deliveries assigned to this driver yet.</p>
                                @endforelse
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
