@extends('layouts.app')

@section('content')
    <section class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
        <div class="space-y-6">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-700">Teams 1, 2, And 3</p>
            <div class="space-y-4">
                <h1 class="max-w-3xl text-4xl font-black leading-tight text-stone-900 sm:text-5xl">
                    One readable MVP flow across subscription, box, and delivery.
                </h1>
                <p class="max-w-2xl text-lg leading-8 text-stone-600">
                    Team 1 owns authentication and subscriptions. Team 2 starts once a subscription creates the current box.
                    Team 3 starts once that same box creates a delivery record. The modules are chained on purpose.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-stone-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-stone-700">Open dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="rounded-full bg-stone-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-stone-700">Start with an account</a>
                    <a href="{{ route('login') }}" class="rounded-full border border-stone-300 px-6 py-3 text-sm font-semibold text-stone-800 transition hover:bg-stone-200/70">Sign in</a>
                @endauth
            </div>
        </div>

        <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-700">System snapshot</p>
                    <h2 class="mt-2 text-2xl font-black text-stone-900">Platform baseline</h2>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-amber-800">MVP</span>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-stone-100 p-4">
                    <p class="text-sm font-semibold text-stone-900">Team 1: Core system</p>
                    <p class="mt-1 text-sm text-stone-600">Auth, roles, subscriptions, billing basics, addresses, and audit logs.</p>
                </div>
                <div class="rounded-2xl bg-stone-100 p-4">
                    <p class="text-sm font-semibold text-stone-900">Team 2: Box workflow</p>
                    <p class="mt-1 text-sm text-stone-600">A new subscription auto-creates the current box with starter items.</p>
                </div>
                <div class="rounded-2xl bg-stone-100 p-4">
                    <p class="text-sm font-semibold text-stone-900">Team 3: Delivery workflow</p>
                    <p class="mt-1 text-sm text-stone-600">That box auto-creates a delivery record with status tracking.</p>
                </div>
                <div class="rounded-2xl bg-stone-100 p-4">
                    <p class="text-sm font-semibold text-stone-900">How to see them</p>
                    <p class="mt-1 text-sm text-stone-600">Seed data, log in, add an address, create a subscription, then open boxes and deliveries.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-12 grid gap-6 lg:grid-cols-3">
        <article class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-700">Team 1</p>
            <h2 class="mt-3 text-2xl font-black text-stone-900">Subscription entry</h2>
            <p class="mt-3 text-sm leading-7 text-stone-600">Register, log in, manage addresses, and start the subscription lifecycle from one place.</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('plans.index') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-800 transition hover:bg-stone-100">Plans</a>
                @auth
                    <a href="{{ route('subscriptions.index') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-800 transition hover:bg-stone-100">Subscriptions</a>
                @endif
            </div>
        </article>

        <article class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-700">Team 2</p>
            <h2 class="mt-3 text-2xl font-black text-stone-900">Current box</h2>
            <p class="mt-3 text-sm leading-7 text-stone-600">Once a subscription exists, the app provisions the current box and exposes the box and customization pages.</p>
            <div class="mt-5 flex flex-wrap gap-3">
                @auth
                    <a href="{{ route('boxes.index') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-800 transition hover:bg-stone-100">Boxes</a>
                @else
                    <span class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-500">Login required</span>
                @endauth
            </div>
        </article>

        <article class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-700">Team 3</p>
            <h2 class="mt-3 text-2xl font-black text-stone-900">Delivery tracking</h2>
            <p class="mt-3 text-sm leading-7 text-stone-600">The delivery record is created from the same subscription-to-box flow and is visible from the delivery board.</p>
            <div class="mt-5 flex flex-wrap gap-3">
                @auth
                    <a href="{{ route('deliveries.index') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-800 transition hover:bg-stone-100">Deliveries</a>
                @else
                    <span class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-500">Login required</span>
                @endauth
            </div>
        </article>
    </section>

    <section class="mt-12">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Plans</p>
                <h2 class="mt-2 text-3xl font-black text-stone-900">Seeded subscription tiers</h2>
            </div>
            <a href="{{ route('plans.index') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-800 transition hover:bg-stone-200/70">See details</a>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach ($plans as $plan)
                <article class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-700">{{ $plan->name }}</p>
                    <p class="mt-4 text-4xl font-black text-stone-900">${{ number_format((float) $plan->price_monthly, 2) }}</p>
                    <p class="mt-1 text-sm text-stone-500">per month</p>
                    <dl class="mt-6 space-y-3 text-sm text-stone-600">
                        <div class="flex items-center justify-between">
                            <dt>Max items</dt>
                            <dd class="font-semibold text-stone-900">{{ $plan->max_items }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Weight cap</dt>
                            <dd class="font-semibold text-stone-900">{{ number_format($plan->max_weight_g) }} g</dd>
                        </div>
                    </dl>
                </article>
            @endforeach
        </div>
    </section>
@endsection
