@extends('layouts.app')

@section('content')
    <section class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">{{ $isAdminView ? 'Admin delivery board' : 'Delivery tracking' }}</p>
        <h1 class="mt-3 text-3xl font-black text-stone-900">{{ $isAdminView ? 'All deliveries' : 'My deliveries' }}</h1>
        <p class="mt-2 text-sm leading-7 text-stone-600">Phase 1 keeps this simple: a shared list of deliveries, clear statuses, and direct links into each record.</p>

        <div class="mt-8 space-y-4">
            @forelse ($deliveries as $delivery)
                <article class="rounded-[1.5rem] bg-stone-100 p-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-stone-900">{{ $delivery->tracking_number ?? 'Tracking pending' }}</p>
                            <p class="text-sm text-stone-600">Status: {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</p>
                            <p class="text-sm text-stone-600">Estimated delivery: {{ $delivery->estimated_delivery?->format('M d, Y') ?? 'TBD' }}</p>
                            <p class="text-sm text-stone-600">Address: {{ $delivery->address?->street ?? 'No address assigned' }}</p>
                            @if ($isAdminView)
                                <p class="text-sm text-stone-600">Subscriber: {{ $delivery->box?->subscription?->user?->email ?? 'Unknown' }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-black uppercase tracking-[0.2em] text-stone-700 shadow-sm">{{ $delivery->box?->theme ?? 'Delivery' }}</span>
                            <a href="{{ route('deliveries.show', $delivery) }}" class="rounded-2xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-700">View details</a>
                        </div>
                    </div>
                </article>
            @empty
                <p class="rounded-2xl bg-stone-100 px-4 py-4 text-sm text-stone-600">No deliveries available yet.</p>
            @endforelse
        </div>
    </section>
@endsection
