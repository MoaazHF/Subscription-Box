@php($title = 'Boxes')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">
                <div>
                    <p class="air-kicker">Team 2 workflow</p>
                    <h1 class="air-title">Current boxes and customization windows.</h1>
                    <p class="air-copy">Each subscription provisions a current box. This surface shows the active theme, lock date, and whether the user can still swap or remove items.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="air-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Boxes</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ink">{{ $boxes->count() }}</p>
                    </div>
                    <div class="air-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Open now</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ink">{{ $boxes->where('status', '!=', 'locked')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-3 lg:grid-cols-2">
            @forelse ($boxes as $box)
                @php($isLocked = $box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast()))

                <article class="air-grid-card overflow-hidden">
                    <div class="air-photo flex h-52 flex-col justify-between bg-[radial-gradient(circle_at_top_right,_rgba(255,56,92,0.16),_transparent_36%),linear-gradient(180deg,_#ffffff_0%,_#f7f7f7_100%)] p-5">
                        <div class="flex items-center justify-between gap-3">
                            <span class="{{ $isLocked ? 'inline-flex items-center rounded-full border border-danger/15 bg-danger/5 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-danger' : 'air-chip-dark' }}">
                                {{ $isLocked ? 'Locked' : 'Open' }}
                            </span>
                            <span class="air-chip">{{ $box->theme ?? 'Standard Theme' }}</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Delivery period</p>
                            <p class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ink">
                                {{ DateTime::createFromFormat('!m', $box->period_month)->format('F') }} {{ $box->period_year }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Lock date</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $box->lock_date?->format('M d, Y') ?? 'N/A' }}</p>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Status</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ ucfirst($box->status) }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('boxes.show', $box->id) }}" class="air-button-primary">View box</a>
                        @if (! $isLocked)
                            <a href="{{ route('boxes.customize', $box) }}" class="air-button-secondary">Customize</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="air-panel-soft xl:col-span-3 lg:col-span-2">
                    <p class="text-sm text-ash">No boxes found for this account yet.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
