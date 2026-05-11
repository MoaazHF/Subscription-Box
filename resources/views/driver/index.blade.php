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
                @php($progressPercent = $delivery->progressPercent())
                @php($progressStep = $delivery->driverProgressStep())
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

                    <div class="mt-6 border-t border-hairline pt-6">
                        <div>
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Driver progress</p>
                                <p class="text-xs font-semibold text-ink">{{ $progressPercent }}%</p>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-cloud">
                                <div class="h-2 rounded-full bg-rausch transition-all duration-300" style="width: {{ $progressPercent }}%;"></div>
                            </div>
                        </div>

                        <form action="{{ route('driver.deliveries.status', $delivery->id) }}" method="POST" class="mt-5 space-y-4">
                            @csrf
                            @method('PATCH')

                            <div class="space-y-2">
                                <label for="progress_step_{{ $delivery->id }}" class="text-sm font-semibold text-ink">Update journey stage</label>
                                <input
                                    id="progress_step_{{ $delivery->id }}"
                                    type="range"
                                    name="progress_step"
                                    min="0"
                                    max="{{ \App\Models\Delivery::MAX_DRIVER_PROGRESS_STEP }}"
                                    step="1"
                                    value="{{ old('progress_step', $progressStep) }}"
                                    class="w-full accent-rausch"
                                >
                                <div class="grid grid-cols-3 gap-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-mute sm:grid-cols-6">
                                    <span>Pending</span>
                                    <span>Picking</span>
                                    <span>Packed</span>
                                    <span>Shipped</span>
                                    <span>On route</span>
                                    <span>Delivered</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="air-button-primary">Save progress</button>
                                <button type="submit" name="status" value="undeliverable" class="air-button-secondary !border-danger/30 !text-danger hover:!bg-danger/5">Report issue</button>
                            </div>
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
