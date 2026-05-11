@php($title = 'Deliveries')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">
                <div>
                    <p class="air-kicker">{{ $isAdminView ? 'Admin delivery board' : 'Delivery tracking' }}</p>
                    <h1 class="air-title">{{ $isAdminView ? 'All deliveries across subscribers.' : 'My active delivery records.' }}</h1>
                    <p class="air-copy">Delivery operations stay direct: status, address, estimated date, and a clear path into the full record for updates or review.</p>
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
                    @php($progressPercent = $delivery->progressPercent())
                    <article class="air-grid-card">
                        <div class="grid gap-5 lg:grid-cols-[1.1fr_0.9fr] lg:items-stretch">
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

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Progress</p>
                                        <p class="text-xs font-semibold text-ink">{{ $progressPercent }}%</p>
                                    </div>
                                    <div class="h-2 rounded-full bg-cloud">
                                        <div class="h-2 rounded-full bg-rausch transition-all duration-300" style="width: {{ $progressPercent }}%;"></div>
                                    </div>
                                </div>

                                @if ($isAdminView)
                                    <p class="text-sm text-ash">Subscriber: {{ $delivery->box?->subscription?->user?->email ?? 'Unknown' }}</p>
                                @endif
                            </div>

                            <div class="group relative overflow-hidden rounded-[28px] border border-hairline/80 bg-white p-5 shadow-[0_20px_50px_-34px_rgba(17,24,39,0.4)]">
                                <div class="pointer-events-none absolute -right-16 -top-16 h-44 w-44 rounded-full bg-rausch/12 blur-2xl transition-transform duration-500 group-hover:scale-110"></div>

                                <div class="relative flex items-center justify-between gap-3">
                                    <span class="air-chip">Box journey</span>
                                    <span class="air-chip-dark">{{ $delivery->box?->period_month }}/{{ $delivery->box?->period_year }}</span>
                                </div>

                                <div class="relative mt-5">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-mute">Current box</p>
                                    <p class="mt-2 text-lg font-semibold tracking-[-0.01em] text-ink">{{ $delivery->box?->theme ?? 'Standard box' }}</p>
                                </div>

                                <div class="relative mt-5 space-y-2">
                                    <div class="flex items-center justify-between text-xs font-semibold">
                                        <span class="uppercase tracking-[0.14em] text-mute">Delivery progress</span>
                                        <span class="text-ink">{{ $progressPercent }}%</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-cloud">
                                        <div class="h-2 rounded-full bg-rausch transition-all duration-500" style="width: {{ $progressPercent }}%;"></div>
                                    </div>
                                </div>

                                <div class="relative mt-5">
                                    <a href="{{ route('deliveries.show', $delivery) }}" class="air-button-primary w-full text-center">View delivery</a>
                                </div>
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
