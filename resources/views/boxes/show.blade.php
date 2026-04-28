@php($title = 'Box Details')

@extends('layouts.app')

@section('content')
    @php($isLocked = $box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast()))

    <section class="space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('boxes.index') }}" class="air-button-secondary">Back to boxes</a>
            @if (! $isLocked)
                <a href="{{ route('boxes.customize', $box) }}" class="air-button-primary">Customize this box</a>
            @endif
        </div>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <div class="air-panel overflow-hidden">
                    <div class="air-photo flex min-h-[300px] flex-col justify-between bg-[radial-gradient(circle_at_top_right,_rgba(255,56,92,0.18),_transparent_34%),linear-gradient(180deg,_#ffffff_0%,_#f7f7f7_100%)] p-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="{{ $isLocked ? 'inline-flex items-center rounded-full border border-danger/15 bg-danger/5 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-danger' : 'air-chip-dark' }}">
                                {{ $isLocked ? 'Locked' : 'Open for customization' }}
                            </span>
                            <span class="air-chip">{{ $box->theme ?? 'Standard Theme' }}</span>
                        </div>

                        <div>
                            <p class="air-kicker">Team 2 detail</p>
                            <h1 class="mt-3 text-4xl font-semibold tracking-[-0.03em] text-ink">
                                {{ DateTime::createFromFormat('!m', $box->period_month)->format('F') }} {{ $box->period_year }} box
                            </h1>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-ash">The box view keeps the customization state, total weight, and included items visible without burying the next action.</p>
                        </div>
                    </div>
                </div>

                <div class="air-panel">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="air-kicker">Included items</p>
                            <h2 class="air-title">What the subscriber will receive.</h2>
                        </div>
                        <span class="air-chip-dark">{{ $box->items->count() }} item{{ $box->items->count() === 1 ? '' : 's' }}</span>
                    </div>

                    <div class="mt-8 grid gap-5 md:grid-cols-2">
                        @forelse ($box->items as $item)
                            <article class="air-grid-card overflow-hidden">
                                <div class="air-photo flex h-44 items-center justify-center bg-[radial-gradient(circle_at_top,_rgba(255,56,92,0.10),_transparent_30%),linear-gradient(180deg,_#ffffff_0%,_#f7f7f7_100%)]">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full border border-hairline bg-canvas text-2xl text-ink">◫</div>
                                </div>

                                <div class="mt-5 space-y-3">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <h3 class="text-lg font-semibold tracking-[-0.01em] text-ink">{{ $item->name ?? 'Unknown item' }}</h3>
                                        <span class="air-chip">{{ $item->weight_g ? $item->weight_g.'g' : 'Weight N/A' }}</span>
                                    </div>
                                    <p class="text-sm leading-7 text-ash">{{ $item->description ?? 'No description available.' }}</p>
                                </div>
                            </article>
                        @empty
                            <div class="air-panel-soft md:col-span-2">
                                <p class="text-sm text-ash">This box does not have any items assigned yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <aside class="space-y-6 xl:sticky xl:top-32 xl:self-start">
                <div class="air-panel">
                    <p class="air-kicker">Box summary</p>
                    <h2 class="air-title">Shipping and lock state.</h2>

                    <div class="mt-6 grid gap-3">
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Lock date</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $box->lock_date?->format('F j, Y') ?? 'N/A' }}</p>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Total weight</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ number_format($box->total_weight_g / 1000, 2) }} kg</p>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Shipping tier</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ ucfirst($box->shipping_tier) }}</p>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Customization</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $isLocked ? 'Closed' : 'Open' }}</p>
                        </div>
                    </div>

                    @if (! $isLocked)
                        <a href="{{ route('boxes.customize', $box) }}" class="air-button-primary mt-6 w-full">Open customization</a>
                    @endif
                </div>
            </aside>
        </section>
    </section>
@endsection
