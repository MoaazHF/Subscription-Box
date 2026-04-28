@php($title = 'Driver Dashboard')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold tracking-[-0.02em] text-ink">My Deliveries</h1>
        </div>

        @if(session('success'))
            <div class="rounded-xl bg-plus/10 px-4 py-3 text-sm text-plus">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6">
            @forelse($deliveries as $delivery)
                <div class="air-panel">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <span class="air-chip mb-2">{{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</span>
                            <h2 class="text-xl font-semibold text-ink">{{ $delivery->box->subscription->user->name }}</h2>
                            <p class="mt-1 text-sm text-ash">{{ $delivery->address->street }}, {{ $delivery->address->city }}, {{ $delivery->address->country }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Estimated</p>
                            <p class="mt-1 text-sm font-semibold text-ink">{{ $delivery->estimated_delivery?->format('M d, Y') ?? 'N/A' }}</p>
                            @if($delivery->eco_dispatch)
                                <span class="mt-2 inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">🌱 Eco Shipping</span>
                            @endif
                        </div>
                    </div>

                    @if($delivery->delivery_instructions)
                        <div class="mt-4 rounded-xl bg-cloud p-4 text-sm text-ink">
                            <span class="font-semibold text-ash block mb-1 text-xs uppercase tracking-widest">Driver Notes</span>
                            {{ $delivery->delivery_instructions }}
                        </div>
                    @endif

                    <div class="mt-6 flex gap-3 border-t border-hairline pt-6">
                        @if($delivery->status !== 'out_for_delivery')
                            <form action="{{ route('driver.deliveries.status', $delivery->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="out_for_delivery">
                                <button type="submit" class="air-button-secondary">Mark Out for Delivery</button>
                            </form>
                        @endif

                        <form action="{{ route('driver.deliveries.status', $delivery->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" class="air-button-primary">Mark Delivered</button>
                        </form>

                        <form action="{{ route('driver.deliveries.status', $delivery->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="failed">
                            <button type="submit" class="air-button-secondary !border-danger/30 !text-danger hover:!bg-danger/5">Report Issue</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="air-panel-soft text-center py-12">
                    <p class="text-ash">You have no pending deliveries.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
