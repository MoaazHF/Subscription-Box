@php($title = 'Delivery Zones Control Panel')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="air-kicker">Admin operations</p>
                    <h1 class="air-title">Delivery Zones Control Panel</h1>
                    <p class="air-copy">Manage serviceable regions and toggle zone availability for delivery operations.</p>
                </div>
                <span class="air-chip-dark">{{ $zones->count() }} zones</span>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Zone setup</p>
                    <h2 class="air-title">Create delivery zone.</h2>
                </div>

                <form method="POST" action="{{ route('delivery-zones.store') }}" class="grid gap-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="name" class="text-sm font-semibold text-ink">Zone name</label>
                        <input id="name" name="name" type="text" class="air-input" placeholder="North Cairo Zone" required>
                    </div>

                    <div class="space-y-2">
                        <label for="region" class="text-sm font-semibold text-ink">Region</label>
                        <input id="region" name="region" type="text" class="air-input" placeholder="Cairo Governorate">
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="country" class="text-sm font-semibold text-ink">Country code</label>
                            <input id="country" name="country" type="text" maxlength="2" class="air-input" placeholder="EG" required>
                        </div>
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-ink sm:pt-9">
                            <input type="checkbox" name="is_serviceable" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" checked>
                            Serviceable
                        </label>
                    </div>

                    <button type="submit" class="air-button-primary w-full">Create delivery zone</button>
                </form>
            </div>

            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Current zones</p>
                    <h2 class="air-title">Service availability map.</h2>
                </div>

                <div class="space-y-4">
                    @forelse ($zones as $zone)
                        <article class="air-grid-card">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-lg font-semibold text-ink">{{ $zone->name }}</p>
                                    <p class="text-sm text-ash">{{ $zone->region ?? 'No region' }} · {{ $zone->country }}</p>
                                </div>
                                <span class="{{ $zone->is_serviceable ? 'air-chip-dark' : 'air-chip' }}">{{ $zone->is_serviceable ? 'Serviceable' : 'Not Serviceable' }}</span>
                            </div>

                            <form method="POST" action="{{ route('delivery-zones.toggle', $zone) }}" class="mt-4">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="air-button-secondary">Toggle Serviceability</button>
                            </form>
                        </article>
                    @empty
                        <div class="air-panel-soft">
                            <p class="text-sm text-ash">No delivery zones available yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
