@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Subscription flow</p>
                <h1 class="mt-3 text-3xl font-black text-stone-900">Start a subscription</h1>
                <p class="mt-2 text-sm leading-7 text-stone-600">This is a simple one-subscription flow. Pick a plan, choose an address, and the billing record is created automatically.</p>

                <form method="POST" action="{{ route('subscriptions.store') }}" class="mt-8 space-y-4">
                    @csrf
                    @if ($addresses->isEmpty())
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            Add at least one address before creating a subscription.
                        </div>
                    @endif
                    <div class="space-y-2">
                        <label for="plan_id" class="text-sm font-semibold text-stone-800">Plan</label>
                        <select id="plan_id" name="plan_id" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}">{{ ucfirst($plan->name) }} - ${{ number_format((float) $plan->price_monthly, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="address_id" class="text-sm font-semibold text-stone-800">Address</label>
                        <select id="address_id" name="address_id" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white">
                            @forelse ($addresses as $address)
                                <option value="{{ $address->id }}">{{ $address->street }} - {{ $address->city }} {{ $address->country }}</option>
                            @empty
                                <option value="">No address available yet</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="start_date" class="text-sm font-semibold text-stone-800">Start date</label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', now()->toDateString()) }}" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white">
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-stone-700">
                            <input type="checkbox" name="auto_renew" value="1" class="h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500" checked>
                            Auto renew
                        </label>
                        <label class="inline-flex items-center gap-3 text-sm font-medium text-stone-700">
                            <input type="checkbox" name="eco_shipping" value="1" class="h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500">
                            Eco shipping
                        </label>
                    </div>
                    <button type="submit" class="w-full rounded-2xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-700 disabled:cursor-not-allowed disabled:bg-stone-400" @disabled($addresses->isEmpty())>Start subscription</button>
                </form>
            </div>

            <div class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Current records</p>
                <h2 class="mt-3 text-3xl font-black text-stone-900">Manage subscriptions</h2>

                <div class="mt-8 space-y-5">
                    @forelse ($subscriptions as $subscription)
                        <article class="rounded-[1.5rem] bg-stone-100 p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-stone-900">{{ ucfirst($subscription->plan?->name ?? 'Plan') }} plan</p>
                                    <p class="mt-1 text-sm text-stone-600">Status: {{ ucfirst($subscription->status) }}</p>
                                    <p class="mt-1 text-sm text-stone-600">Next billing: {{ optional($subscription->next_billing_date)->format('M d, Y') ?? 'N/A' }}</p>
                                    <p class="mt-1 text-sm text-stone-600">Address: {{ $subscription->address?->street ?? 'Not assigned' }}</p>
                                </div>
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-black uppercase tracking-[0.2em] text-stone-700 shadow-sm">{{ $subscription->payments->count() }} payment{{ $subscription->payments->count() === 1 ? '' : 's' }}</span>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                @if ($subscription->status === 'active')
                                    <form method="POST" action="{{ route('subscriptions.pause', $subscription) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-2xl border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 transition hover:bg-white">Pause</button>
                                    </form>
                                @endif

                                @if ($subscription->status === 'paused')
                                    <form method="POST" action="{{ route('subscriptions.resume', $subscription) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-2xl border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 transition hover:bg-white">Resume</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('subscriptions.change-plan', $subscription) }}" class="flex flex-wrap gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="plan_id" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500">
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}" @selected($subscription->plan_id === $plan->id)>{{ ucfirst($plan->name) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="rounded-2xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-700">Change plan</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl bg-stone-100 px-4 py-4 text-sm text-stone-600">No subscriptions yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
