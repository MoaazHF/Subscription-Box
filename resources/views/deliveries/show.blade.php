@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Delivery detail</p>
                <h1 class="mt-2 text-3xl font-black text-stone-900">{{ $delivery->tracking_number ?? 'Delivery record' }}</h1>
            </div>
            <a href="{{ route('deliveries.index') }}" class="rounded-2xl border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 transition hover:bg-stone-100">Back to deliveries</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <section class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Status</p>
                <h2 class="mt-3 text-2xl font-black text-stone-900">{{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</h2>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-stone-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Estimated</p>
                        <p class="mt-2 text-sm font-semibold text-stone-900">{{ $delivery->estimated_delivery?->format('M d, Y') ?? 'TBD' }}</p>
                    </div>
                    <div class="rounded-2xl bg-stone-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Delivered At</p>
                        <p class="mt-2 text-sm font-semibold text-stone-900">{{ $delivery->actual_delivery?->format('M d, Y H:i') ?? '--' }}</p>
                    </div>
                    <div class="rounded-2xl bg-stone-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Stops Remaining</p>
                        <p class="mt-2 text-sm font-semibold text-stone-900">{{ $delivery->stops_remaining ?? 'Not set' }}</p>
                    </div>
                    <div class="rounded-2xl bg-stone-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Eco Dispatch</p>
                        <p class="mt-2 text-sm font-semibold text-stone-900">{{ $delivery->eco_dispatch ? 'Enabled' : 'Disabled' }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Address and box</p>
                <div class="mt-6 space-y-4 text-sm text-stone-600">
                    <div>
                        <p class="font-semibold text-stone-900">Delivery address</p>
                        <p class="mt-1">{{ $delivery->address?->street ?? 'No street assigned' }}</p>
                        <p>{{ $delivery->address?->city ?? '' }} {{ $delivery->address?->country ?? '' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-stone-900">Box</p>
                        <p class="mt-1">{{ $delivery->box?->theme ?? 'Standard box' }}</p>
                        <p>Period: {{ $delivery->box?->period_month }}/{{ $delivery->box?->period_year }}</p>
                    </div>
                    <div>
                        <p class="font-semibold text-stone-900">Instructions</p>
                        <p class="mt-1">{{ $delivery->delivery_instructions ?? 'None provided.' }}</p>
                    </div>
                </div>
            </section>
        </div>

        @if (auth()->user()->isAdmin())
            <section class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Admin update</p>
                <h2 class="mt-3 text-2xl font-black text-stone-900">Basic status update</h2>

                <form method="POST" action="{{ route('deliveries.update-status', $delivery) }}" class="mt-6 grid gap-4 lg:grid-cols-2">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-2">
                        <label for="status" class="text-sm font-semibold text-stone-800">Status</label>
                        <select id="status" name="status" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white">
                            @foreach (\App\Models\Delivery::STATUSES as $status)
                                <option value="{{ $status }}" @selected(old('status', $delivery->status) === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="tracking_number" class="text-sm font-semibold text-stone-800">Tracking number</label>
                        <input id="tracking_number" name="tracking_number" type="text" value="{{ old('tracking_number', $delivery->tracking_number) }}" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white">
                    </div>

                    <div class="space-y-2">
                        <label for="estimated_delivery" class="text-sm font-semibold text-stone-800">Estimated delivery</label>
                        <input id="estimated_delivery" name="estimated_delivery" type="date" value="{{ old('estimated_delivery', $delivery->estimated_delivery?->toDateString()) }}" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white">
                    </div>

                    <div class="space-y-2">
                        <label for="delivery_instructions" class="text-sm font-semibold text-stone-800">Instructions</label>
                        <input id="delivery_instructions" name="delivery_instructions" type="text" value="{{ old('delivery_instructions', $delivery->delivery_instructions) }}" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white">
                    </div>

                    <label class="inline-flex items-center gap-3 text-sm font-medium text-stone-700">
                        <input type="checkbox" name="eco_dispatch" value="1" class="h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500" @checked(old('eco_dispatch', $delivery->eco_dispatch))>
                        Eco dispatch
                    </label>

                    <div>
                        <button type="submit" class="rounded-2xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-700">Save delivery update</button>
                    </div>
                </form>
            </section>
        @endif
    </section>
@endsection
