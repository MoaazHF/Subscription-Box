@php($title = 'Claims Control Panel')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <p class="air-kicker">Admin operations</p>
            <h1 class="air-title">Claims Control Panel</h1>
        </div>

        <div class="air-panel">
            <form method="GET" action="{{ route('admin-claims.index') }}" class="grid gap-3 md:grid-cols-4">
                <input name="q" type="text" class="air-input" placeholder="Search description or subscriber" value="{{ $filters['q'] }}">
                <select name="type" class="air-select">
                    <option value="">All types</option>
                    <option value="damaged" @selected($filters['type'] === 'damaged')>Damaged</option>
                    <option value="missing" @selected($filters['type'] === 'missing')>Missing</option>
                </select>
                <select name="status" class="air-select">
                    <option value="">All statuses</option>
                    <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                    <option value="resolved" @selected($filters['status'] === 'resolved')>Resolved</option>
                    <option value="rejected" @selected($filters['status'] === 'rejected')>Rejected</option>
                </select>
                <button type="submit" class="air-button-primary">Filter</button>
            </form>
        </div>

        <div class="space-y-4">
            @forelse ($claims as $claim)
                <article class="air-grid-card">
                    <div class="flex flex-wrap justify-between gap-2">
                        <p class="font-semibold text-ink">{{ ucfirst($claim->type) }} claim</p>
                        <span class="air-chip">{{ ucfirst($claim->status) }}</span>
                    </div>
                    <p class="mt-2 text-sm text-ash">{{ $claim->description }}</p>
                    <p class="mt-2 text-xs text-ash">Subscriber: {{ $claim->subscription?->user?->email ?? 'Unknown' }}</p>
                    <p class="text-xs text-ash">Submitted: {{ $claim->submitted_at?->format('M d, Y H:i') }}</p>
                    @if ($claim->resolvedBy)
                        <p class="text-xs text-ash">Resolved by: {{ $claim->resolvedBy->email }} at {{ $claim->resolved_at?->format('M d, Y H:i') }}</p>
                    @endif

                    @if ($claim->status === 'pending')
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <form method="POST" action="{{ route('admin-claims.resolve', $claim) }}" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <input name="resolution_notes" type="text" class="air-input" placeholder="Resolution notes">
                                <button type="submit" class="air-button-primary w-full">Resolve</button>
                            </form>
                            <form method="POST" action="{{ route('admin-claims.reject', $claim) }}" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <input name="resolution_notes" type="text" class="air-input" placeholder="Rejection reason">
                                <button type="submit" class="air-button-danger w-full">Reject</button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <div class="air-panel-soft">
                    <p class="text-sm text-ash">No claims found.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
