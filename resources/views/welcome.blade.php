@extends('layouts.app')

@section('content')
    <section class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr] xl:items-center">
        <div class="space-y-8">
            <div class="space-y-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Platform Journey</p>
                <h1 class="max-w-4xl text-5xl font-bold tracking-[-0.05em] text-ink sm:text-6xl">
                    Subscription, box, and delivery flow in one magazine-style surface.
                </h1>
                <p class="max-w-2xl text-base leading-8 text-ash">
                    A complete subscription-commerce experience with customer onboarding, curated box operations,
                    and real-time delivery visibility presented in a single interface.
                </p>
            </div>

            <div class="air-float overflow-hidden rounded-[32px] border border-hairline bg-canvas">
                <div class="grid gap-4 p-3 md:grid-cols-[1fr_1fr_1fr_auto]">
                    <div class="rounded-[24px] bg-cloud px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Where</p>
                        <p class="mt-2 text-sm font-semibold text-ink">Subscription and account setup</p>
                    </div>
                    <div class="rounded-[24px] bg-cloud px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">When</p>
                        <p class="mt-2 text-sm font-semibold text-ink">Current month box provision</p>
                    </div>
                    <div class="rounded-[24px] bg-cloud px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Who</p>
                        <p class="mt-2 text-sm font-semibold text-ink">Delivery board and admin updates</p>
                    </div>
                    <div class="flex items-center justify-center">
                        @auth
                            <a href="{{ route('dashboard') }}" class="flex h-16 w-16 items-center justify-center rounded-full bg-rausch text-xl font-bold text-white transition hover:bg-rausch-deep">→</a>
                        @else
                            <a href="{{ route('register') }}" class="flex h-16 w-16 items-center justify-center rounded-full bg-rausch text-xl font-bold text-white transition hover:bg-rausch-deep">→</a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-[24px] border border-hairline bg-canvas p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-rausch">Account</p>
                    <h2 class="mt-3 text-2xl font-bold tracking-[-0.03em] text-ink">Customer foundation</h2>
                    <p class="mt-3 text-sm leading-7 text-ash">Roles, addresses, subscriptions, payments, and audit logs.</p>
                </div>
                <div class="rounded-[24px] border border-hairline bg-canvas p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-rausch">Fulfillment</p>
                    <h2 class="mt-3 text-2xl font-bold tracking-[-0.03em] text-ink">Curated box</h2>
                    <p class="mt-3 text-sm leading-7 text-ash">Starter items, box detail, and customization entry points.</p>
                </div>
                <div class="rounded-[24px] border border-hairline bg-canvas p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-rausch">Logistics</p>
                    <h2 class="mt-3 text-2xl font-bold tracking-[-0.03em] text-ink">Delivery operations</h2>
                    <p class="mt-3 text-sm leading-7 text-ash">Tracking board, delivery detail, and admin status changes.</p>
                </div>
            </div>
        </div>

        <div class="air-float overflow-hidden rounded-[32px] border border-hairline bg-canvas">
            <div class="aspect-[4/3] bg-[linear-gradient(145deg,#fff1f4_0%,#ffffff_36%,#f7f7f7_100%)] p-8">
                <div class="flex h-full flex-col justify-between">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Magazine panel</p>
                            <h2 class="mt-3 max-w-sm text-3xl font-bold tracking-[-0.04em] text-ink">Built for customers, operations, and support teams.</h2>
                        </div>
                        <img src="{{ asset('AppIcon.png') }}" alt="Subscription Box app icon" class="h-24 w-24 rounded-[28px] object-cover ring-1 ring-white/80 shadow-[0_24px_60px_rgba(255,56,92,0.18)]">
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-[24px] bg-canvas/90 p-4 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Flow</p>
                            <p class="mt-2 text-sm font-semibold text-ink">Address → Subscription</p>
                        </div>
                        <div class="rounded-[24px] bg-canvas/90 p-4 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Auto-create</p>
                            <p class="mt-2 text-sm font-semibold text-ink">Box → Delivery</p>
                        </div>
                        <div class="rounded-[24px] bg-canvas/90 p-4 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Views</p>
                            <p class="mt-2 text-sm font-semibold text-ink">Subscriber + Admin</p>
                        </div>
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
