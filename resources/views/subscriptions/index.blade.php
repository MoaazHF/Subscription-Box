@php($title = 'Subscriptions')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="air-panel overflow-hidden">
                <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                    <div>
                        <p class="air-kicker">Team 1 workflow</p>
                        <h1 class="air-title">Start the subscription lifecycle.</h1>
                        <p class="air-copy">Choose a plan, bind it to a saved address, and let the system generate the first billing record and the downstream box workflow.</p>

                        <div class="mt-8 rounded-[26px] border border-hairline bg-cloud p-3">
                            <div class="grid gap-3 md:grid-cols-[1fr_1fr_1fr_auto] md:items-end">
                                <div class="rounded-[22px] bg-canvas px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Plan</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $plans->count() }} available tiers</p>
                                </div>
                                <div class="rounded-[22px] bg-canvas px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Address</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $addresses->count() }} saved destinations</p>
                                </div>
                                <div class="rounded-[22px] bg-canvas px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Records</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $subscriptions->count() }} subscription{{ $subscriptions->count() === 1 ? '' : 's' }}</p>
                                </div>
                                <div class="flex justify-end">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-rausch text-lg text-white">→</span>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('subscriptions.store') }}" class="mt-8 space-y-5">
                            @csrf

                            @if ($addresses->isEmpty())
                                <div class="rounded-[22px] border border-danger/15 bg-danger/5 px-4 py-3 text-sm text-danger">
                                    Add an address first. The subscription flow depends on a valid destination.
                                </div>
                            @endif

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label for="plan_id" class="text-sm font-semibold text-ink">Plan</label>
                                    <select id="plan_id" name="plan_id" class="air-select">
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ ucfirst($plan->name) }} · ${{ number_format((float) $plan->price_monthly, 2) }}/month</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label for="address_id" class="text-sm font-semibold text-ink">Address</label>
                                    <select id="address_id" name="address_id" class="air-select">
                                        @forelse ($addresses as $address)
                                            <option value="{{ $address->id }}">{{ $address->street }} · {{ $address->city }} {{ $address->country }}</option>
                                        @empty
                                            <option value="">No address available yet</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-[0.9fr_1.1fr]">
                                <div class="space-y-2">
                                    <label for="start_date" class="text-sm font-semibold text-ink">Start date</label>
                                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date', now()->toDateString()) }}" class="air-input">
                                </div>

                                <div class="rounded-[24px] border border-hairline bg-cloud px-5 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Options</p>
                                    <div class="mt-4 flex flex-wrap gap-5">
                                        <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                            <input type="checkbox" name="auto_renew" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" checked>
                                            Auto renew
                                        </label>
                                        <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                            <input type="checkbox" name="eco_shipping" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch">
                                            Eco shipping
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="air-button-primary w-full disabled:cursor-not-allowed disabled:bg-mute" @disabled($addresses->isEmpty())>
                                Start subscription
                            </button>
                        </form>
                    </div>

                    <div class="air-photo flex min-h-[340px] flex-col justify-between bg-[radial-gradient(circle_at_top_right,_rgba(255,56,92,0.20),_transparent_32%),linear-gradient(180deg,_#ffffff_0%,_#f7f7f7_100%)] p-6">
                        <div class="flex items-center justify-between">
                            <span class="air-chip">Readable MVP</span>
                            <span class="air-chip">Billing + provisioning</span>
                        </div>
                        <div class="space-y-4">
                            <img src="{{ asset('AppIcon.png') }}" alt="Subscription Box icon" class="h-20 w-20 rounded-[24px] object-cover ring-1 ring-hairline">
                            <div>
                                <p class="text-2xl font-semibold tracking-[-0.02em] text-ink">One action starts three team flows.</p>
                                <p class="mt-3 text-sm leading-7 text-ash">Subscription creation drives payments, current box generation, and delivery provisioning without extra screens or hidden steps.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="air-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="air-kicker">Current records</p>
                        <h2 class="air-title">Manage active subscriptions.</h2>
                    </div>
                    <span class="air-chip-dark">{{ $subscriptions->count() }} total</span>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($subscriptions as $subscription)
                        <article class="air-grid-card space-y-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="space-y-2">
                                    <p class="text-lg font-semibold tracking-[-0.01em] text-ink">{{ ucfirst($subscription->plan?->name ?? 'Plan') }} plan</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="air-chip">{{ ucfirst($subscription->status) }}</span>
                                        <span class="air-chip">{{ $subscription->payments->count() }} payment{{ $subscription->payments->count() === 1 ? '' : 's' }}</span>
                                    </div>
                                </div>
                                <div class="text-sm text-ash">
                                    <p>Next billing</p>
                                    <p class="mt-1 font-semibold text-ink">{{ optional($subscription->next_billing_date)->format('M d, Y') ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Address</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $subscription->address?->street ?? 'Not assigned' }}</p>
                                </div>
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Renewal mode</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $subscription->auto_renew ? 'Automatic' : 'Manual' }}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @if ($subscription->status === 'active')
                                    <form method="POST" action="{{ route('subscriptions.pause', $subscription) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="air-button-secondary">Pause</button>
                                    </form>
                                @endif

                                @if ($subscription->status === 'paused')
                                    <form method="POST" action="{{ route('subscriptions.resume', $subscription) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="air-button-secondary">Resume</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('subscriptions.change-plan', $subscription) }}" class="flex flex-1 flex-wrap gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="plan_id" class="air-select min-w-[220px] flex-1">
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}" @selected($subscription->plan_id === $plan->id)>{{ ucfirst($plan->name) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="air-button-primary">Change plan</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="air-panel-soft">
                            <p class="text-sm text-ash">No subscriptions exist yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </section>
@endsection
