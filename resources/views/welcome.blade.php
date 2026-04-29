@extends('layouts.app')

@section('content')
    <section class="air-float overflow-hidden rounded-[36px] border border-hairline bg-canvas">
        <div class="grid gap-8 bg-[radial-gradient(circle_at_top_right,rgba(255,56,92,0.12),transparent_34%),linear-gradient(180deg,#ffffff_0%,#f8f8f8_100%)] p-8 lg:grid-cols-[1.15fr_0.85fr] lg:p-12">
            <div class="space-y-7">
                <div class="space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Subscription Commerce Platform</p>
                    <h1 class="max-w-4xl text-5xl font-bold tracking-[-0.05em] text-ink sm:text-6xl">Run subscriptions, curate boxes, and track deliveries from one dashboard.</h1>
                    <p class="max-w-2xl text-base leading-8 text-ash">
                        Deliver a complete customer experience with integrated billing, monthly box fulfillment, and operational delivery visibility.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="air-button-primary px-6">Open Dashboard</a>
                        <a href="{{ route('plans.index') }}" class="air-button-secondary px-6">Explore Plans</a>
                    @else
                        <a href="{{ route('register') }}" class="air-button-primary px-6">Create Account</a>
                        <a href="{{ route('plans.index') }}" class="air-button-secondary px-6">View Pricing</a>
                    @endauth
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-[22px] border border-hairline bg-canvas/90 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-ash">Automation</p>
                        <p class="mt-2 text-sm font-semibold text-ink">Subscription to box provisioning</p>
                    </div>
                    <div class="rounded-[22px] border border-hairline bg-canvas/90 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-ash">Operations</p>
                        <p class="mt-2 text-sm font-semibold text-ink">Driver and delivery control</p>
                    </div>
                    <div class="rounded-[22px] border border-hairline bg-canvas/90 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-ash">Support</p>
                        <p class="mt-2 text-sm font-semibold text-ink">Claims and audit visibility</p>
                    </div>
                </div>
            </div>

            <div class="air-photo flex min-h-[360px] flex-col justify-between bg-[linear-gradient(145deg,#fff1f4_0%,#ffffff_42%,#f7f7f7_100%)] p-7">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Live Operations</p>
                        <h2 class="mt-3 max-w-sm text-3xl font-bold tracking-[-0.04em] text-ink">Built for teams that ship every month.</h2>
                    </div>
                    <img src="{{ asset('AppIcon.png') }}" alt="Subscription Box app icon" class="h-24 w-24 rounded-[28px] object-cover ring-1 ring-white/80 shadow-[0_24px_60px_rgba(255,56,92,0.18)]">
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[20px] border border-hairline bg-canvas p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-ash">Flow</p>
                        <p class="mt-2 text-sm font-semibold text-ink">Account → Subscription → Box</p>
                    </div>
                    <div class="rounded-[20px] border border-hairline bg-canvas p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-ash">Execution</p>
                        <p class="mt-2 text-sm font-semibold text-ink">Delivery assignment → Tracking</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-14">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Built Surfaces</p>
                <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">Open the product by module</h2>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <article class="overflow-hidden rounded-[28px] border border-hairline bg-canvas">
                <div class="aspect-[4/3] bg-[linear-gradient(160deg,#fff1f4_0%,#ffffff_55%,#f7f7f7_100%)] p-6">
                    <div class="flex h-full flex-col justify-between">
                        <span class="inline-flex w-fit rounded-full border border-hairline bg-canvas px-3 py-1 text-xs font-semibold text-ink">Account</span>
                        <div>
                            <h3 class="text-[1.35rem] font-bold tracking-[-0.03em] text-ink">Subscription entry</h3>
                            <p class="mt-2 text-sm leading-7 text-ash">Register, sign in, manage addresses, and start the lifecycle from one place.</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-3 p-6">
                    <a href="{{ route('plans.index') }}" class="inline-flex items-center rounded-full border border-hairline bg-canvas px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-cloud">Review plans</a>
                    @auth
                        <a href="{{ route('subscriptions.index') }}" class="ml-2 inline-flex items-center rounded-full bg-rausch px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rausch-deep">Manage subscriptions</a>
                    @endif
                </div>
            </article>

            <article class="overflow-hidden rounded-[28px] border border-hairline bg-canvas">
                <div class="aspect-[4/3] bg-[linear-gradient(160deg,#ffffff_0%,#f7f7f7_52%,#edf3ff_100%)] p-6">
                    <div class="flex h-full flex-col justify-between">
                        <span class="inline-flex w-fit rounded-full border border-hairline bg-canvas px-3 py-1 text-xs font-semibold text-ink">Fulfillment</span>
                        <div>
                            <h3 class="text-[1.35rem] font-bold tracking-[-0.03em] text-ink">Current box</h3>
                            <p class="mt-2 text-sm leading-7 text-ash">Once a subscription exists, the app provisions the current box and starter items automatically.</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-3 p-6">
                    @auth
                        <a href="{{ route('boxes.index') }}" class="inline-flex items-center rounded-full bg-rausch px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rausch-deep">Open boxes</a>
                    @else
                        <span class="inline-flex items-center rounded-full border border-hairline bg-canvas px-4 py-2.5 text-sm font-semibold text-mute">Login required</span>
                    @endauth
                </div>
            </article>

            <article class="overflow-hidden rounded-[28px] border border-hairline bg-canvas">
                <div class="aspect-[4/3] bg-[linear-gradient(160deg,#ffffff_0%,#f7f7f7_48%,#fff4f7_100%)] p-6">
                    <div class="flex h-full flex-col justify-between">
                        <span class="inline-flex w-fit rounded-full border border-hairline bg-canvas px-3 py-1 text-xs font-semibold text-ink">Logistics</span>
                        <div>
                            <h3 class="text-[1.35rem] font-bold tracking-[-0.03em] text-ink">Delivery tracking</h3>
                            <p class="mt-2 text-sm leading-7 text-ash">The same flow also creates a delivery record, subscriber tracking page, and admin update path.</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-3 p-6">
                    @auth
                        <a href="{{ route('deliveries.index') }}" class="inline-flex items-center rounded-full bg-rausch px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rausch-deep">Track deliveries</a>
                    @else
                        <span class="inline-flex items-center rounded-full border border-hairline bg-canvas px-4 py-2.5 text-sm font-semibold text-mute">Login required</span>
                    @endauth
                </div>
            </article>
        </div>
    </section>

    <section class="mt-14">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Plans</p>
                <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">Seeded subscription tiers</h2>
            </div>
            <a href="{{ route('plans.index') }}" class="inline-flex items-center rounded-full border border-hairline bg-canvas px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-cloud">See all details</a>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach ($plans as $plan)
                <article class="overflow-hidden rounded-[28px] border border-hairline bg-canvas">
                    <div class="aspect-[4/3] p-6 {{ $loop->first ? 'bg-[linear-gradient(160deg,#fff4f7_0%,#ffffff_60%,#f7f7f7_100%)]' : ($loop->last ? 'bg-[linear-gradient(160deg,#f7efff_0%,#ffffff_58%,#f7f7f7_100%)]' : 'bg-[linear-gradient(160deg,#fff8f3_0%,#ffffff_58%,#f7f7f7_100%)]') }}">
                        <div class="flex h-full flex-col justify-between">
                            <span class="inline-flex w-fit rounded-full border border-hairline bg-canvas px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-ink">{{ $plan->name }}</span>
                            <div>
                                <p class="text-4xl font-bold tracking-[-0.05em] text-ink">${{ number_format((float) $plan->price_monthly, 2) }}</p>
                                <p class="mt-1 text-sm text-ash">per month</p>
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
            @endforeach
        </div>
    </section>
@endsection
