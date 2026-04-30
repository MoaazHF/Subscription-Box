@php($title = 'Subscriptions Control Panel')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="air-kicker">Admin operations</p>
                    <h1 class="air-title">Subscriptions CRUD Control Panel</h1>
                    <p class="air-copy">Manage subscriber subscriptions without using customer checkout flow.</p>
                </div>
                <span class="air-chip-dark">{{ $subscriptions->count() }} records</span>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Create subscription</p>
                    <h2 class="air-title">Create for a subscriber.</h2>
                </div>

                <form method="POST" action="{{ route('admin-subscriptions.store') }}" class="grid gap-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="user_id" class="text-sm font-semibold text-ink">Subscriber</label>
                        <select id="user_id" name="user_id" class="air-select" required>
                            @foreach ($subscribers as $subscriber)
                                <option value="{{ $subscriber->id }}">{{ $subscriber->name }} ({{ $subscriber->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="plan_id" class="text-sm font-semibold text-ink">Plan</label>
                            <select id="plan_id" name="plan_id" class="air-select" required>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ ucfirst($plan->name) }} · ${{ number_format((float) $plan->price_monthly, 2) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label for="address_id" class="text-sm font-semibold text-ink">Address</label>
                            <select id="address_id" name="address_id" class="air-select" required>
                                @foreach ($addresses as $address)
                                    <option value="{{ $address->id }}">{{ $address->user?->email ?? 'Unknown' }} · {{ $address->street }} · {{ $address->city }} {{ $address->country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="start_date" class="text-sm font-semibold text-ink">Start date</label>
                        <input id="start_date" name="start_date" type="date" class="air-input" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                            <input type="checkbox" name="auto_renew" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" checked>
                            Auto renew
                        </label>
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                            <input type="checkbox" name="eco_shipping" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch">
                            Eco shipping
                        </label>
                    </div>

                    <button type="submit" class="air-button-primary w-full">Create subscription</button>
                </form>
            </div>

            <div class="air-panel space-y-6">
                <div>
                    <p class="air-kicker">Current subscriptions</p>
                    <h2 class="air-title">Update or delete records.</h2>
                </div>

                <div class="space-y-4">
                    @forelse ($subscriptions as $subscription)
                        <article class="air-grid-card space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-lg font-semibold text-ink">{{ $subscription->user?->name ?? 'Unknown user' }}</p>
                                    <p class="text-sm text-ash">{{ $subscription->user?->email ?? 'No email' }} · {{ ucfirst($subscription->plan?->name ?? 'Unknown plan') }}</p>
                                </div>
                                <span class="air-chip">{{ ucfirst($subscription->status) }}</span>
                            </div>

                            <form method="POST" action="{{ route('admin-subscriptions.update', $subscription) }}" class="grid gap-3">
                                @csrf
                                @method('PATCH')

                                <div class="grid gap-3 sm:grid-cols-3">
                                    <select name="plan_id" class="air-select" required>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}" @selected($subscription->plan_id === $plan->id)>{{ ucfirst($plan->name) }}</option>
                                        @endforeach
                                    </select>

                                    <select name="address_id" class="air-select" required>
                                        @foreach ($addresses->where('user_id', $subscription->user_id) as $address)
                                            <option value="{{ $address->id }}" @selected($subscription->address_id === $address->id)>{{ $address->street }} · {{ $address->city }}</option>
                                        @endforeach
                                    </select>

                                    <select name="status" class="air-select" required>
                                        @foreach (['active', 'paused', 'cancelled', 'suspended', 'gift'] as $status)
                                            <option value="{{ $status }}" @selected($subscription->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex flex-wrap gap-4">
                                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                        <input type="checkbox" name="auto_renew" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" @checked($subscription->auto_renew)>
                                        Auto renew
                                    </label>
                                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                        <input type="checkbox" name="eco_shipping" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" @checked($subscription->eco_shipping)>
                                        Eco shipping
                                    </label>
                                </div>

                                <button type="submit" class="air-button-primary">Update</button>
                            </form>

                            <form method="POST" action="{{ route('admin-subscriptions.destroy', $subscription) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="air-button-danger">Delete</button>
                            </form>
                        </article>
                    @empty
                        <div class="air-panel-soft">
                            <p class="text-sm text-ash">No subscription records yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
