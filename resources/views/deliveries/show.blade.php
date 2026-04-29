@php($title = 'Delivery Details')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="air-kicker">Delivery detail</p>
                <h1 class="air-title">{{ $delivery->tracking_number ?? 'Delivery record' }}</h1>
            </div>
            <a href="{{ route('deliveries.index') }}" class="air-button-secondary">Back to deliveries</a>
        </div>

        <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="space-y-6">
                <div class="air-panel">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="air-chip-dark">{{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</span>
                        <span class="air-chip">{{ $delivery->box?->theme ?? 'Standard box' }}</span>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Estimated</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $delivery->estimated_delivery?->format('M d, Y') ?? 'TBD' }}</p>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Delivered at</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $delivery->actual_delivery?->format('M d, Y H:i') ?? '--' }}</p>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Stops remaining</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $delivery->stops_remaining ?? 'Not set' }}</p>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Eco dispatch</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $delivery->eco_dispatch ? 'Enabled' : 'Disabled' }}</p>
                        </div>
                    </div>
                </div>

                <div class="air-panel">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <p class="air-kicker">Address</p>
                            <h2 class="air-title">Destination and instructions.</h2>
                            <div class="mt-6 space-y-4 text-sm text-ash">
                                <div>
                                    <p class="font-semibold text-ink">Delivery address</p>
                                    <p class="mt-2">{{ $delivery->address?->street ?? 'No street assigned' }}</p>
                                    <p>{{ $delivery->address?->city ?? '' }} {{ $delivery->address?->country ?? '' }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-ink">Instructions</p>
                                    <p class="mt-2">{{ $delivery->delivery_instructions ?? 'None provided.' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="air-photo flex min-h-[240px] flex-col justify-between bg-[radial-gradient(circle_at_top_right,_rgba(255,56,92,0.18),_transparent_34%),linear-gradient(180deg,_#ffffff_0%,_#f7f7f7_100%)] p-6">
                            <div class="flex items-center justify-between gap-3">
                                <span class="air-chip">Box context</span>
                                <span class="air-chip">{{ $delivery->box?->period_month }}/{{ $delivery->box?->period_year }}</span>
                            </div>
                            <div>
                                <p class="text-2xl font-semibold tracking-[-0.02em] text-ink">{{ $delivery->box?->theme ?? 'Standard box' }}</p>
                                <p class="mt-3 text-sm leading-7 text-ash">Delivery and box records stay linked, so support teams can trace shipping progress back to the exact subscription period.</p>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                @if($delivery->claims->isNotEmpty())
                    <div class="air-panel mt-6">
                        <p class="air-kicker">History</p>
                        <h2 class="air-title">Submitted Claims</h2>
                        <div class="mt-6 space-y-4">
                            @foreach($delivery->claims as $claim)
                                <div class="rounded-[24px] border border-hairline p-5">
                                    <div class="flex flex-wrap items-center gap-3 mb-3">
                                        <span class="air-chip font-semibold">{{ ucfirst($claim->type) }}</span>
                                        <span class="air-chip-dark {{ $claim->status === 'resolved' ? '!bg-plus/10 !text-plus !border-plus/20' : '!bg-warning/10 !text-warning !border-warning/20' }}">
                                            {{ ucfirst($claim->status) }}
                                        </span>
                                        <span class="text-xs text-ash ml-auto">{{ $claim->submitted_at->format('M d, Y g:i A') }}</span>
                                    </div>
                                    <p class="text-sm text-ink leading-7">{{ $claim->description }}</p>
                                    @if($claim->photo_url)
                                        <div class="mt-4">
                                            <a href="{{ $claim->photo_url }}" target="_blank" class="text-sm text-rausch hover:underline font-semibold">View Photo Evidence &rarr;</a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="air-panel mt-6">
                    <p class="air-kicker">Support</p>
                    <h2 class="air-title">File a Claim.</h2>
                    <form action="{{ route('deliveries.claims.store', $delivery->id) }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="type" class="text-sm font-semibold text-ink">Issue Type</label>
                                <select id="type" name="type" class="air-select">
                                    <option value="damaged">Damaged</option>
                                    <option value="missing">Missing Box/Item</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label for="photo" class="text-sm font-semibold text-ink">Photo Evidence (Optional)</label>
                                <input id="photo" name="photo" type="file" class="air-input" accept="image/*">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label for="description" class="text-sm font-semibold text-ink">Description</label>
                            <textarea id="description" name="description" class="air-textarea" required></textarea>
                        </div>
                        <button type="submit" class="air-button-primary">Submit Claim</button>
                    </form>
                </div>
            </div>

            @if (auth()->user()->isAdmin())
                <aside class="space-y-6 xl:sticky xl:top-32 xl:self-start">
                    <div class="air-panel">
                        <p class="air-kicker">Admin update</p>
                        <h2 class="air-title">Basic status update.</h2>

                        <form method="POST" action="{{ route('deliveries.update-status', $delivery) }}" class="mt-6 space-y-4">
                            @csrf
                            @method('PATCH')

                            <div class="space-y-2">
                                <label for="status" class="text-sm font-semibold text-ink">Status</label>
                                <select id="status" name="status" class="air-select">
                                    @foreach (\App\Models\Delivery::STATUSES as $status)
                                        <option value="{{ $status }}" @selected(old('status', $delivery->status) === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="tracking_number" class="text-sm font-semibold text-ink">Tracking number</label>
                                <input id="tracking_number" name="tracking_number" type="text" value="{{ old('tracking_number', $delivery->tracking_number) }}" class="air-input">
                            </div>

                            <div class="space-y-2">
                                <label for="estimated_delivery" class="text-sm font-semibold text-ink">Estimated delivery</label>
                                <input id="estimated_delivery" name="estimated_delivery" type="date" value="{{ old('estimated_delivery', $delivery->estimated_delivery?->toDateString()) }}" class="air-input">
                            </div>

                            <div class="space-y-2">
                                <label for="delivery_instructions" class="text-sm font-semibold text-ink">Instructions</label>
                                <input id="delivery_instructions" name="delivery_instructions" type="text" value="{{ old('delivery_instructions', $delivery->delivery_instructions) }}" class="air-input">
                            </div>

                            <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                <input type="checkbox" name="eco_dispatch" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" @checked(old('eco_dispatch', $delivery->eco_dispatch))>
                                Eco dispatch
                            </label>

                            <button type="submit" class="air-button-primary w-full">Save delivery update</button>
                        </form>
                    </div>
                </aside>
            @endif
        </section>
    </section>
@endsection
