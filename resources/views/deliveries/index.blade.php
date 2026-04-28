@php($title = 'Deliveries')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">
                <div>
                    <p class="air-kicker">{{ $isAdminView ? 'Admin delivery board' : 'Delivery tracking' }}</p>
                    <h1 class="air-title">{{ $isAdminView ? 'All deliveries across subscribers.' : 'My active delivery records.' }}</h1>
                    <p class="air-copy">Phase 1 keeps the delivery workflow direct: status, address, estimated date, and a clear path into the full record for updates or review.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="air-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Records</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ink">{{ $deliveries->count() }}</p>
                    </div>
                    <div class="air-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">With tracking</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ink">{{ $deliveries->whereNotNull('tracking_number')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                @forelse ($deliveries as $delivery)
                    <article class="air-grid-card">
                        <div class="grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-lg font-semibold tracking-[-0.01em] text-ink">{{ $delivery->tracking_number ?? 'Tracking pending' }}</p>
                                    <span class="air-chip">{{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</span>
                                    <span class="air-chip">{{ $delivery->box?->theme ?? 'Delivery' }}</span>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Estimated delivery</p>
                                        <p class="mt-2 text-sm font-semibold text-ink">{{ $delivery->estimated_delivery?->format('M d, Y') ?? 'TBD' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Address</p>
                                        <p class="mt-2 text-sm font-semibold text-ink">{{ $delivery->address?->street ?? 'No address assigned' }}</p>
                                    </div>
                                </div>

                                @if ($isAdminView)
                                    <p class="text-sm text-ash">Subscriber: {{ $delivery->box?->subscription?->user?->email ?? 'Unknown' }}</p>
                                @endif
                            </div>

                            <div class="flex items-center justify-start lg:justify-end">
                                <a href="{{ route('deliveries.show', $delivery) }}" class="air-button-primary">View delivery</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="air-panel-soft">
                        <p class="text-sm text-ash">No deliveries available yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
