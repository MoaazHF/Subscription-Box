@extends('layouts.app')

@section('content')
    <section class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
        <div class="space-y-6">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-700">Core System And Subscription</p>
            <div class="space-y-4">
                <h1 class="max-w-3xl text-4xl font-black leading-tight text-stone-900 sm:text-5xl">
                    Simple subscription management for the team to build on without guessing the rules.
                </h1>
                <p class="max-w-2xl text-lg leading-8 text-stone-600">
                    This foundation covers authentication, roles, subscriptions, billing basics, addresses, and audit logging.
                    Team 2 and Team 3 can build on top of it without redefining the customer lifecycle.
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
                    <h2 class="mt-2 text-2xl font-black text-stone-900">Team 1 baseline</h2>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-amber-800">MVP</span>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-stone-100 p-4">
                    <p class="text-sm font-semibold text-stone-900">Auth and roles</p>
                    <p class="mt-1 text-sm text-stone-600">Manual login, registration, and admin-only audit access.</p>
                </div>
                <div class="rounded-2xl bg-stone-100 p-4">
                    <p class="text-sm font-semibold text-stone-900">Subscriptions</p>
                    <p class="mt-1 text-sm text-stone-600">Create, pause, resume, and switch plans with readable state changes.</p>
                </div>
                <div class="rounded-2xl bg-stone-100 p-4">
                    <p class="text-sm font-semibold text-stone-900">Billing basics</p>
                    <p class="mt-1 text-sm text-stone-600">Simple payment records with tax calculation and renewal handling.</p>
                </div>
                <div class="rounded-2xl bg-stone-100 p-4">
                    <p class="text-sm font-semibold text-stone-900">Shared foundation</p>
                    <p class="mt-1 text-sm text-stone-600">Addresses, plans, and logs are ready for the rest of the team.</p>
                </div>
            </div>
        </div>
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
