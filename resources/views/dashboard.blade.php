@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Platform dashboard</p>
                <h1 class="mt-3 text-3xl font-black text-stone-900">Hello, {{ $user->name }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-7 text-stone-600">
                    This page is the shared entry point for Team 1 subscriptions, Team 2 boxes, and Team 3 deliveries.
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl bg-stone-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Role</p>
                        <p class="mt-2 text-xl font-black text-stone-900">{{ ucfirst(str_replace('_', ' ', $user->role?->name ?? 'guest')) }}</p>
                    </div>
                    <div class="rounded-2xl bg-stone-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Addresses</p>
                        <p class="mt-2 text-xl font-black text-stone-900">{{ $user->addresses->count() }}</p>
                    </div>
                    <div class="rounded-2xl bg-stone-100 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-stone-500">Subscriptions</p>
                        <p class="mt-2 text-xl font-black text-stone-900">{{ $user->subscriptions->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-stone-200 bg-stone-900 p-8 text-white shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-300">Current status</p>
                @if ($currentSubscription)
                    <h2 class="mt-3 text-2xl font-black">{{ ucfirst($currentSubscription->status) }} subscription</h2>
                    <p class="mt-2 text-sm leading-7 text-stone-300">
                        {{ ucfirst($currentSubscription->plan?->name ?? 'Unknown') }} plan, next billing on
                        {{ optional($currentSubscription->next_billing_date)->format('M d, Y') ?? 'not scheduled' }}.
                    </p>
                    <div class="mt-6 space-y-3 text-sm text-stone-200">
                        <p>Address: {{ $currentSubscription->address?->street ?? 'Not assigned yet' }}</p>
                        <p>Auto renew: {{ $currentSubscription->auto_renew ? 'Enabled' : 'Disabled' }}</p>
                        <p>Loyalty points: {{ $currentSubscription->loyalty_points }}</p>
                    </div>
                @else
                    <h2 class="mt-3 text-2xl font-black">No subscription yet</h2>
                    <p class="mt-2 text-sm leading-7 text-stone-300">Add an address, choose a plan, and start a subscription from the subscriptions page.</p>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_1fr]">
            <section class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Quick links</p>
                        <h2 class="mt-2 text-2xl font-black text-stone-900">Workflows</h2>
                    </div>
                </div>
                <div class="mt-6 grid gap-4">
                    <a href="{{ route('addresses.index') }}" class="rounded-2xl bg-stone-100 px-4 py-4 text-sm font-semibold text-stone-900 transition hover:bg-stone-200/80">Manage addresses</a>
                    <a href="{{ route('plans.index') }}" class="rounded-2xl bg-stone-100 px-4 py-4 text-sm font-semibold text-stone-900 transition hover:bg-stone-200/80">Review active plans</a>
                    <a href="{{ route('subscriptions.index') }}" class="rounded-2xl bg-stone-100 px-4 py-4 text-sm font-semibold text-stone-900 transition hover:bg-stone-200/80">Manage subscriptions</a>
                    <a href="{{ route('boxes.index') }}" class="rounded-2xl bg-stone-100 px-4 py-4 text-sm font-semibold text-stone-900 transition hover:bg-stone-200/80">Open current boxes</a>
                    <a href="{{ route('deliveries.index') }}" class="rounded-2xl bg-stone-100 px-4 py-4 text-sm font-semibold text-stone-900 transition hover:bg-stone-200/80">Track deliveries</a>
                    <a href="{{ route('payments.index') }}" class="rounded-2xl bg-stone-100 px-4 py-4 text-sm font-semibold text-stone-900 transition hover:bg-stone-200/80">See billing history</a>
                </div>
            </section>

            <section class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Recent payments</p>
                        <h2 class="mt-2 text-2xl font-black text-stone-900">Latest activity</h2>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse ($recentPayments as $payment)
                        <article class="rounded-2xl bg-stone-100 px-4 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-stone-900">{{ ucfirst($payment->subscription->plan?->name ?? 'Plan') }} payment</p>
                                    <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-500">{{ str_replace('_', ' ', $payment->gateway_reason_code) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-stone-900">${{ number_format((float) $payment->amount, 2) }}</p>
                                    <p class="mt-1 text-xs text-stone-500">{{ $payment->created_at?->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl bg-stone-100 px-4 py-4 text-sm text-stone-600">No payment records yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection
