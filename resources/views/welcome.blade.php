@extends('layouts.app')

@section('content')
    <section class="relative isolate mx-auto max-w-[1200px] overflow-hidden rounded-[36px] border border-hairline bg-canvas">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('HeroSection.png') }}');"></div>
        <div class="absolute inset-0 bg-[linear-gradient(120deg,rgba(16,24,40,0.78)_0%,rgba(16,24,40,0.65)_42%,rgba(16,24,40,0.45)_100%)]"></div>

        <div class="relative grid min-h-[78vh] items-center gap-10 p-8 md:p-12 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="flex flex-col items-center justify-center gap-7 pb-4 text-center">
                <div class="space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-white/90">Subscription Platform</p>
                    <h1 class="max-w-4xl text-5xl font-bold tracking-[-0.05em] text-white sm:text-6xl">Operate your subscription business from one production-ready workspace.</h1>
                    <p class="max-w-2xl text-base leading-8 text-white/85">
                        Billing, fulfillment, delivery dispatch, claims, and customer engagement work together through one connected workflow.
                    </p>
                </div>

                <div class="flex flex-wrap justify-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="air-button-primary cursor-pointer px-6">Open Dashboard</a>
                        <a href="{{ route('plans.index') }}" class="air-button-secondary cursor-pointer border-white/40 bg-white/10 px-6 text-white hover:bg-white/20">Explore Plans</a>
                    @else
                        <a href="{{ route('register') }}" class="air-button-primary cursor-pointer px-6">Create Account</a>
                        <a href="{{ route('plans.index') }}" class="air-button-secondary cursor-pointer border-white/40 bg-white/10 px-6 text-white hover:bg-white/20">View Pricing</a>
                    @endauth
                </div>
            </div>

            <div class="flex items-end justify-center lg:items-center">
                <div class="w-full max-w-md space-y-4 rounded-[28px] border border-white/25 bg-white/15 p-6 backdrop-blur-md">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-white/80">Live Snapshot</p>
                            <p class="mt-2 text-2xl font-bold tracking-[-0.03em] text-white">Operations at a glance</p>
                        </div>
                        <img src="{{ asset('AppIcon.png') }}" alt="Subscription Box app icon" class="h-16 w-16 rounded-[18px] object-cover ring-1 ring-white/70 shadow-[0_16px_40px_rgba(15,23,42,0.34)]">
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Subscriber Flow</p>
                            <p class="mt-2 text-sm font-semibold text-white">Account → Subscription → Box</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Delivery Flow</p>
                            <p class="mt-2 text-sm font-semibold text-white">Dispatch → Route → Proof</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto mt-14 max-w-[1200px]">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">What We Do</p>
            <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">From subscription signup to doorstep delivery, the platform executes the full cycle.</h2>
            <p class="mt-3 text-base leading-8 text-ash">Each stage keeps data consistent between customer-facing screens and operations dashboards, so teams move faster with fewer manual handoffs.</p>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <article class="rounded-[28px] border border-hairline bg-canvas p-6 text-center">
                <p class="inline-flex rounded-full border border-hairline bg-cloud px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-ink">01</p>
                <h3 class="mt-4 text-[1.35rem] font-bold tracking-[-0.03em] text-ink">Acquire & Subscribe</h3>
                <p class="mt-2 text-sm leading-7 text-ash">Customers register, pick plans, set addresses, and activate subscription billing with payment tracking and lifecycle states.</p>
                <a href="{{ route('plans.index') }}" class="mt-5 inline-flex items-center rounded-full border border-hairline px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-cloud cursor-pointer">Review plans</a>
            </article>

            <article class="rounded-[28px] border border-hairline bg-canvas p-6 text-center">
                <p class="inline-flex rounded-full border border-hairline bg-cloud px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-ink">02</p>
                <h3 class="mt-4 text-[1.35rem] font-bold tracking-[-0.03em] text-ink">Curate & Fulfill</h3>
                <p class="mt-2 text-sm leading-7 text-ash">Monthly boxes are generated, customized, packed with tracked inventory, and prepared for dispatch with status visibility.</p>
                @auth
                    <a href="{{ route('boxes.index') }}" class="mt-5 inline-flex items-center rounded-full border border-hairline px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-cloud cursor-pointer">Open boxes</a>
                @endif
            </article>

            <article class="rounded-[28px] border border-hairline bg-canvas p-6 text-center">
                <p class="inline-flex rounded-full border border-hairline bg-cloud px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-ink">03</p>
                <h3 class="mt-4 text-[1.35rem] font-bold tracking-[-0.03em] text-ink">Dispatch & Support</h3>
                <p class="mt-2 text-sm leading-7 text-ash">Drivers and delivery records handle last-mile execution, while claims, notifications, and audit logs provide operational control.</p>
                @auth
                    <a href="{{ route('deliveries.index') }}" class="mt-5 inline-flex items-center rounded-full border border-hairline px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-cloud cursor-pointer">Track deliveries</a>
                @endif
            </article>
        </div>
    </section>

    <section class="mx-auto mt-14 max-w-[1200px]">
        <div class="flex flex-col items-center justify-center gap-4 text-center">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Plans</p>
                <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">Seeded subscription tiers</h2>
            </div>
            <a href="{{ route('plans.index') }}" class="inline-flex cursor-pointer items-center rounded-full border border-hairline bg-canvas px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-cloud">See all details</a>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach ($plans as $plan)
                @php
                    $planActionUrl = auth()->check() ? route('subscriptions.index') : route('register');
                    $planKey = strtolower(trim($plan->name));
                    $planBackgrounds = [
                        'basic' => asset('basic.png'),
                        'standard' => asset('standrad.png'),
                        'premium' => asset('premium.png'),
                    ];
                    $planBackground = $planBackgrounds[$planKey] ?? null;
                    $fallbackGradient = $loop->first
                        ? 'bg-[linear-gradient(160deg,#fff4f7_0%,#ffffff_60%,#f7f7f7_100%)]'
                        : ($loop->last
                            ? 'bg-[linear-gradient(160deg,#f7efff_0%,#ffffff_58%,#f7f7f7_100%)]'
                            : 'bg-[linear-gradient(160deg,#fff8f3_0%,#ffffff_58%,#f7f7f7_100%)]');
                    $headerStyle = $planBackground
                        ? "background-image: linear-gradient(160deg, rgba(15, 23, 42, 0.52) 0%, rgba(15, 23, 42, 0.28) 45%, rgba(15, 23, 42, 0.14) 100%), url('{$planBackground}'); background-size: cover; background-position: center;"
                        : null;
                @endphp
                <a href="{{ $planActionUrl }}" class="group block cursor-pointer overflow-hidden rounded-[28px] border border-hairline bg-canvas transition hover:-translate-y-1 hover:shadow-[0_16px_36px_rgba(15,23,42,0.12)]">
                    <article>
                        <div @class(['aspect-[4/3] p-6', $fallbackGradient => ! $planBackground]) @if($headerStyle) style="{{ $headerStyle }}" @endif>
                            <div class="flex h-full flex-col justify-between">
                                <span @class([
                                    'inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]',
                                    'border border-white/30 bg-white/20 text-white backdrop-blur-sm' => $planBackground,
                                    'border border-hairline bg-canvas text-ink' => ! $planBackground,
                                ])>{{ $plan->name }}</span>
                                <div>
                                    <p @class([
                                        'text-4xl font-bold tracking-[-0.05em]',
                                        'text-white drop-shadow-[0_8px_24px_rgba(0,0,0,0.45)]' => $planBackground,
                                        'text-ink' => ! $planBackground,
                                    ])>${{ number_format((float) $plan->price_monthly, 2) }}</p>
                                    <p @class([
                                        'mt-1 text-sm',
                                        'text-white/90' => $planBackground,
                                        'text-ash' => ! $planBackground,
                                    ])>per month</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4 p-6">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-ash">Max items</span>
                                <span class="font-semibold text-ink">{{ $plan->max_items }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-ash">Weight cap</span>
                                <span class="font-semibold text-ink">{{ number_format($plan->max_weight_g) }} g</span>
                            </div>
                        </div>
                    </article>
                </a>
            @endforeach
        </div>
    </section>
@endsection
