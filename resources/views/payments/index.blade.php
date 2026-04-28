@php($title = 'Payments')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="air-panel">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">
                <div>
                    <p class="air-kicker">Billing basics</p>
                    <h1 class="air-title">Payment history with readable reasons.</h1>
                    <p class="air-copy">Every subscription start, plan change, and renewal writes a record here. The gateway is simulated, but the internal billing history remains explicit.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="air-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Visible records</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ink">{{ $payments->count() }}</p>
                    </div>
                    <div class="air-stat">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Total records</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.02em] text-ink">{{ $payments->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 grid gap-4">
                @forelse ($payments as $payment)
                    <article class="air-grid-card">
                        <div class="grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-lg font-semibold tracking-[-0.01em] text-ink">{{ ucfirst($payment->subscription->plan?->name ?? 'Plan') }}</p>
                                    <span class="air-chip">{{ ucfirst($payment->status) }}</span>
                                </div>
                                <p class="text-sm leading-7 text-ash">{{ ucwords(str_replace('_', ' ', $payment->gateway_reason_code)) }}</p>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Tax</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">${{ number_format((float) $payment->tax_amount, 2) }}</p>
                                </div>
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Amount</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">${{ number_format((float) $payment->amount, 2) }}</p>
                                </div>
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Date</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $payment->created_at?->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="air-panel-soft">
                        <p class="text-sm text-ash">No payment records yet.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $payments->links() }}
            </div>
        </div>
    </section>
@endsection
