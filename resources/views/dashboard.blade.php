@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="grid gap-6 xl:grid-cols-[1.08fr_0.92fr]">
            <div class="air-float overflow-hidden rounded-[32px] border border-hairline bg-canvas">
                <div class="aspect-[16/9] bg-[linear-gradient(160deg,#fff2f5_0%,#ffffff_56%,#f7f7f7_100%)] p-8">
                    <div class="flex h-full flex-col justify-between">
                        <div class="space-y-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Platform dashboard</p>
                            <h1 class="max-w-3xl text-5xl font-bold tracking-[-0.05em] text-ink">Hello, {{ $user->name }}</h1>
                            <p class="max-w-2xl text-sm leading-7 text-ash">
                                Start in subscriptions, continue into the current box, then move into delivery tracking without leaving the same design system.
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-[24px] bg-canvas/90 p-5 backdrop-blur">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Role</p>
                                <p class="mt-3 text-[1.6rem] font-bold tracking-[-0.03em] text-ink">{{ ucfirst(str_replace('_', ' ', $user->role?->name ?? 'guest')) }}</p>
                            </div>
                            <div class="rounded-[24px] bg-canvas/90 p-5 backdrop-blur">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Addresses</p>
                                <p class="mt-3 text-[1.6rem] font-bold tracking-[-0.03em] text-ink">{{ $user->addresses->count() }}</p>
                            </div>
                            <div class="rounded-[24px] bg-canvas/90 p-5 backdrop-blur">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Subscriptions</p>
                                <p class="mt-3 text-[1.6rem] font-bold tracking-[-0.03em] text-ink">{{ $user->subscriptions->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="air-float rounded-[32px] border border-hairline bg-canvas p-8">
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Current status</p>
                @if ($currentSubscription)
                    <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">{{ ucfirst($currentSubscription->status) }} subscription</h2>
                    <p class="mt-3 text-sm leading-7 text-ash">
                        {{ ucfirst($currentSubscription->plan?->name ?? 'Unknown') }} plan, next billing on
                        {{ optional($currentSubscription->next_billing_date)->format('M d, Y') ?? 'not scheduled' }}.
                    </p>
                    <div class="mt-6 grid gap-4">
                        <div class="rounded-[24px] bg-cloud p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Address</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $currentSubscription->address?->street ?? 'Not assigned yet' }}</p>
                        </div>
                        <div class="rounded-[24px] bg-cloud p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Auto renew</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $currentSubscription->auto_renew ? 'Enabled' : 'Disabled' }}</p>
                        </div>
                        <div class="rounded-[24px] bg-cloud p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Loyalty points</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $currentSubscription->loyalty_points }}</p>
                        </div>
                    </div>
                @else
                    <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">No subscription yet</h2>
                    <p class="mt-3 text-sm leading-7 text-ash">Add an address, choose a plan, and start a subscription from the subscriptions page.</p>
                @endif
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <section class="air-float rounded-[32px] border border-hairline bg-canvas p-8">
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Quick links</p>
                <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">Workflows</h2>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @if ($user->isAdmin())
                        <a href="{{ route('products.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Catalog</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Manage products</p>
                        </a>
                        <a href="{{ route('drivers.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Logistics</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Manage drivers</p>
                        </a>
                        <a href="{{ route('warehouse-staff.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Fulfillment</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Manage warehouse staff</p>
                        </a>
                        <a href="{{ route('delivery-zones.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Coverage</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Manage delivery zones</p>
                        </a>
                        <a href="{{ route('audit-logs.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Governance</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Review audit logs</p>
                        </a>
                    @else
                        <a href="{{ route('addresses.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Account</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Manage addresses</p>
                        </a>
                        <a href="{{ route('plans.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Catalog</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Review plans</p>
                        </a>
                        <a href="{{ route('subscriptions.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Billing</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Manage subscriptions</p>
                        </a>
                        <a href="{{ route('boxes.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Fulfillment</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Open current boxes</p>
                        </a>
                        <a href="{{ route('deliveries.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Logistics</p>
                            <p class="mt-2 text-lg font-semibold text-ink">Track deliveries</p>
                        </a>
                        <a href="{{ route('payments.index') }}" class="rounded-[24px] border border-hairline bg-canvas p-5 transition hover:bg-cloud">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Billing</p>
                            <p class="mt-2 text-lg font-semibold text-ink">See payment history</p>
                        </a>
                    @endif
                </div>
            </section>

            <section class="air-float rounded-[32px] border border-hairline bg-canvas p-8">
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Recent payments</p>
                <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">Latest activity</h2>

                <div class="mt-6 space-y-4">
                    @forelse ($recentPayments as $payment)
                        <article class="rounded-[24px] border border-hairline bg-cloud px-5 py-5">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-ink">{{ ucfirst($payment->subscription->plan?->name ?? 'Plan') }} payment</p>
                                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-ash">{{ str_replace('_', ' ', $payment->gateway_reason_code) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-ink">${{ number_format((float) $payment->amount, 2) }}</p>
                                    <p class="mt-1 text-xs text-ash">{{ $payment->created_at?->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-[24px] border border-hairline bg-cloud px-5 py-5 text-sm text-ash">No payment records yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection
