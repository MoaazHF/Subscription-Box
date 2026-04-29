@php($title = 'Subscriptions')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        @if (session('payment_success'))
            <div id="payment-success-popup" class="fixed right-5 top-24 z-[60] w-full max-w-sm">
                <div class="relative overflow-hidden rounded-[24px] border border-emerald-200 bg-white p-5 shadow-[0_22px_55px_rgba(16,185,129,0.22)]">
                    <div class="pointer-events-none absolute -right-8 -top-8 h-28 w-28 rounded-full bg-emerald-100"></div>
                    <div class="relative flex items-start gap-4">
                        <div class="relative mt-0.5 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500 text-white">
                            <span class="absolute inline-flex h-12 w-12 animate-ping rounded-full bg-emerald-400/60"></span>
                            <svg class="relative h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-600">Payment success</p>
                            <p class="text-base font-semibold text-ink">Subscription transaction approved.</p>
                            <p class="text-sm text-ash">Amount: ${{ session('payment_success.amount') }} · Ref: {{ session('payment_success.reference') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if (session('payment_failed'))
            <div id="payment-failed-popup" class="fixed right-5 top-24 z-[60] w-full max-w-sm">
                <div class="relative overflow-hidden rounded-[24px] border border-red-200 bg-white p-5 shadow-[0_22px_55px_rgba(239,68,68,0.24)]">
                    <div class="pointer-events-none absolute -right-8 -top-8 h-28 w-28 rounded-full bg-red-100"></div>
                    <div class="relative flex items-start gap-4">
                        <div class="relative mt-0.5 flex h-12 w-12 items-center justify-center rounded-full bg-red-500 text-white">
                            <span class="absolute inline-flex h-12 w-12 animate-ping rounded-full bg-red-400/60"></span>
                            <svg class="relative h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-red-600">Payment failed</p>
                            <p class="text-base font-semibold text-ink">Transaction was declined.</p>
                            <p class="text-sm text-ash">Amount: ${{ session('payment_failed.amount') }} · Ref: {{ session('payment_failed.reference') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="air-panel overflow-hidden">
                <div class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                    <div>
                        <p class="air-kicker">Subscription workflow</p>
                        <h1 class="air-title">Start the subscription lifecycle.</h1>
                        <p class="air-copy">Choose a plan, bind it to a saved address, and let the system generate the first billing record and the downstream box workflow.</p>

                        <div class="mt-8 rounded-[26px] border border-hairline bg-cloud p-3">
                            <div class="grid gap-3 md:grid-cols-[1fr_1fr_1fr_auto] md:items-end">
                                <div class="rounded-[22px] bg-canvas px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Plan</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $plans->count() }} available tiers</p>
                                </div>
                                <div class="rounded-[22px] bg-canvas px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Address</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $addresses->count() }} saved destinations</p>
                                </div>
                                <div class="rounded-[22px] bg-canvas px-4 py-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Records</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $subscriptions->count() }} subscription{{ $subscriptions->count() === 1 ? '' : 's' }}</p>
                                </div>
                                <div class="flex justify-end">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-rausch text-lg text-white">→</span>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('subscriptions.store') }}" id="subscription-form" class="mt-8 space-y-5">
                            @csrf

                            @if ($addresses->isEmpty())
                                <div class="rounded-[22px] border border-danger/15 bg-danger/5 px-4 py-3 text-sm text-danger">
                                    Add an address first. The subscription flow depends on a valid destination.
                                </div>
                            @endif

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label for="plan_id" class="text-sm font-semibold text-ink">Plan</label>
                                    <select id="plan_id" name="plan_id" class="air-select">
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}" data-price="{{ number_format((float) $plan->price_monthly, 2, '.', '') }}">{{ ucfirst($plan->name) }} · ${{ number_format((float) $plan->price_monthly, 2) }}/month</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label for="address_id" class="text-sm font-semibold text-ink">Address</label>
                                    <select id="address_id" name="address_id" class="air-select">
                                        @forelse ($addresses as $address)
                                            <option value="{{ $address->id }}">{{ $address->street }} · {{ $address->city }} {{ $address->country }}</option>
                                        @empty
                                            <option value="">No address available yet</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="grid gap-4 md:grid-cols-[0.9fr_1.1fr]">
                                <div class="space-y-2">
                                    <label for="start_date" class="text-sm font-semibold text-ink">Start date</label>
                                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date', now()->toDateString()) }}" class="air-input">
                                </div>

                                <div class="rounded-[24px] border border-hairline bg-cloud px-5 py-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Options</p>
                                    <div class="mt-4 flex flex-wrap gap-5">
                                        <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                            <input type="checkbox" name="auto_renew" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" checked>
                                            Auto renew
                                        </label>
                                        <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                            <input type="checkbox" name="eco_shipping" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch">
                                            Eco shipping
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="payment_gateway_status" name="payment_gateway_status" value="{{ old('payment_gateway_status') }}">
                            <input type="hidden" id="payment_gateway_ref" name="payment_gateway_ref" value="{{ old('payment_gateway_ref') }}">
                            <input type="hidden" id="payment_card_last4" name="payment_card_last4" value="{{ old('payment_card_last4') }}">
                            <input type="hidden" id="payment_gateway_reason" name="payment_gateway_reason" value="{{ old('payment_gateway_reason') }}">

                            <button type="button" id="start-subscription-button" class="air-button-primary w-full disabled:cursor-not-allowed disabled:bg-mute" @disabled($addresses->isEmpty())>
                                Start subscription
                            </button>
                        </form>

                        <div id="gateway-modal" class="fixed inset-0 z-50 hidden">
                            <div class="absolute inset-0 bg-ink/50"></div>
                            <div class="absolute inset-0 flex items-center justify-center p-4">
                                <div class="w-full max-w-xl rounded-[28px] border border-hairline bg-canvas p-6 shadow-[0_24px_60px_rgba(15,23,42,0.24)]">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="air-kicker">Simulated payment gateway</p>
                                            <h3 class="text-2xl font-semibold tracking-[-0.02em] text-ink">Authorize monthly payment</h3>
                                            <p class="mt-2 text-sm text-ash">Use this simulation to approve or decline the transaction. Every attempt is saved in payment records.</p>
                                        </div>
                                        <button type="button" id="gateway-close" class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-hairline text-ink">×</button>
                                    </div>

                                    <div class="mt-6 rounded-[20px] border border-hairline bg-cloud px-4 py-3">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Transaction</p>
                                        <p class="mt-2 text-sm font-semibold text-ink">Plan total: <span id="gateway-plan-total">$0.00</span></p>
                                    </div>

                                    <div class="mt-5 grid gap-4">
                                        <div class="space-y-2">
                                            <label for="gateway_cardholder" class="text-sm font-semibold text-ink">Cardholder name</label>
                                            <input id="gateway_cardholder" type="text" class="air-input" placeholder="Name on card">
                                        </div>
                                        <div class="space-y-2">
                                            <label for="gateway_card_number" class="text-sm font-semibold text-ink">Card number</label>
                                            <input id="gateway_card_number" type="text" inputmode="numeric" class="air-input" placeholder="4242 4242 4242 4242">
                                        </div>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div class="space-y-2">
                                                <label for="gateway_expiry" class="text-sm font-semibold text-ink">Expiry (MM/YY)</label>
                                                <input id="gateway_expiry" type="text" class="air-input" placeholder="12/30">
                                            </div>
                                            <div class="space-y-2">
                                                <label for="gateway_cvv" class="text-sm font-semibold text-ink">CVV</label>
                                                <input id="gateway_cvv" type="text" inputmode="numeric" class="air-input" placeholder="123">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex flex-wrap gap-3">
                                        <button type="button" id="gateway-approve" class="air-button-primary flex-1">Approve payment</button>
                                        <button type="button" id="gateway-decline" class="air-button-danger flex-1">Decline payment</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="air-photo flex min-h-[340px] flex-col justify-between bg-[radial-gradient(circle_at_top_right,_rgba(255,56,92,0.20),_transparent_32%),linear-gradient(180deg,_#ffffff_0%,_#f7f7f7_100%)] p-6">
                        <div class="flex items-center justify-between">
                            <span class="air-chip">Production-ready</span>
                            <span class="air-chip">Billing + provisioning</span>
                        </div>
                        <div class="space-y-4">
                            <img src="{{ asset('AppIcon.png') }}" alt="Subscription Box icon" class="h-20 w-20 rounded-[24px] object-cover ring-1 ring-hairline">
                            <div>
                                <p class="text-2xl font-semibold tracking-[-0.02em] text-ink">One action starts three team flows.</p>
                                <p class="mt-3 text-sm leading-7 text-ash">Subscription creation drives payments, current box generation, and delivery provisioning without extra screens or hidden steps.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="air-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="air-kicker">Current records</p>
                        <h2 class="air-title">Manage active subscriptions.</h2>
                    </div>
                    <span class="air-chip-dark">{{ $subscriptions->count() }} total</span>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($subscriptions as $subscription)
                        <article class="air-grid-card space-y-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="space-y-2">
                                    <p class="text-lg font-semibold tracking-[-0.01em] text-ink">{{ ucfirst($subscription->plan?->name ?? 'Plan') }} plan</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="air-chip">{{ ucfirst($subscription->status) }}</span>
                                        <span class="air-chip">{{ $subscription->payments->count() }} payment{{ $subscription->payments->count() === 1 ? '' : 's' }}</span>
                                    </div>
                                </div>
                                <div class="text-sm text-ash">
                                    <p>Next billing</p>
                                    <p class="mt-1 font-semibold text-ink">{{ optional($subscription->next_billing_date)->format('M d, Y') ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="grid gap-3 md:grid-cols-2">
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Address</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $subscription->address?->street ?? 'Not assigned' }}</p>
                                </div>
                                <div class="air-stat">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Renewal mode</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ $subscription->auto_renew ? 'Automatic' : 'Manual' }}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                @if ($subscription->status === 'active')
                                    <form method="POST" action="{{ route('subscriptions.pause', $subscription) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="air-button-secondary">Pause</button>
                                    </form>
                                @endif

                                @if ($subscription->status === 'paused')
                                    <form method="POST" action="{{ route('subscriptions.resume', $subscription) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="air-button-secondary">Resume</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('subscriptions.change-plan', $subscription) }}" class="flex flex-1 flex-wrap gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="plan_id" class="air-select min-w-[220px] flex-1">
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}" @selected($subscription->plan_id === $plan->id)>{{ ucfirst($plan->name) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="air-button-primary">Change plan</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="air-panel-soft">
                            <p class="text-sm text-ash">No subscriptions exist yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/card-validator@10.0.3/dist/card-validator.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            const successPopup = document.getElementById('payment-success-popup');
            const failedPopup = document.getElementById('payment-failed-popup');
            const form = document.getElementById('subscription-form');
            const openButton = document.getElementById('start-subscription-button');
            const modal = document.getElementById('gateway-modal');
            const closeButton = document.getElementById('gateway-close');
            const approveButton = document.getElementById('gateway-approve');
            const declineButton = document.getElementById('gateway-decline');
            const planSelect = document.getElementById('plan_id');
            const planTotal = document.getElementById('gateway-plan-total');
            const cardholder = document.getElementById('gateway_cardholder');
            const cardNumber = document.getElementById('gateway_card_number');
            const expiry = document.getElementById('gateway_expiry');
            const cvv = document.getElementById('gateway_cvv');
            const statusField = document.getElementById('payment_gateway_status');
            const refField = document.getElementById('payment_gateway_ref');
            const last4Field = document.getElementById('payment_card_last4');
            const reasonField = document.getElementById('payment_gateway_reason');
            const dismissPopup = function (popup) {
                if (!popup) {
                    return;
                }

                window.setTimeout(function () {
                    popup.classList.add('opacity-0', 'translate-y-1', 'transition', 'duration-500');
                    window.setTimeout(function () {
                        popup.remove();
                    }, 520);
                }, 3400);
            };

            if (!form || !openButton || !modal) {
                dismissPopup(successPopup);
                dismissPopup(failedPopup);
                return;
            }

            if (window.Cleave) {
                new window.Cleave(cardNumber, {
                    creditCard: true,
                });

                new window.Cleave(expiry, {
                    date: true,
                    datePattern: ['m', 'y'],
                });

                new window.Cleave(cvv, {
                    numeral: true,
                    blocks: [4],
                    numericOnly: true,
                });
            }

            const updatePlanTotal = function () {
                const selectedOption = planSelect.options[planSelect.selectedIndex];
                const amount = selectedOption ? selectedOption.dataset.price : '0.00';
                planTotal.textContent = '$' + amount;
            };

            const openModal = function () {
                updatePlanTotal();
                modal.classList.remove('hidden');
            };

            const closeModal = function () {
                modal.classList.add('hidden');
            };

            const buildGatewayReference = function () {
                const timestamp = Date.now().toString().slice(-8);
                const random = Math.floor(Math.random() * 9000 + 1000).toString();
                return 'SIM-' + timestamp + '-' + random;
            };

            const validateGatewayInputs = function () {
                const digitsOnly = cardNumber.value.replace(/\D/g, '');
                const trimmedCardholder = cardholder.value.trim();
                const trimmedExpiry = expiry.value.trim();
                const trimmedCvv = cvv.value.replace(/\D/g, '');

                if (!trimmedCardholder) {
                    alert('Cardholder name is required.');
                    return null;
                }

                if (!window.cardValidator) {
                    if (digitsOnly.length < 12 || !trimmedExpiry || trimmedCvv.length < 3) {
                        alert('Complete card number, expiry, and CVV to continue.');
                        return null;
                    }

                    return digitsOnly;
                }

                const cardCheck = window.cardValidator.number(digitsOnly);
                const expiryCheck = window.cardValidator.expirationDate(trimmedExpiry);
                const cvvCheck = window.cardValidator.cvv(trimmedCvv);

                if (!cardCheck.isValid) {
                    alert('Card number is invalid.');
                    return null;
                }

                if (!expiryCheck.isValid) {
                    alert('Expiry date is invalid.');
                    return null;
                }

                if (!cvvCheck.isValid) {
                    alert('CVV is invalid.');
                    return null;
                }

                if (cardCheck.card && cardCheck.card.code && trimmedCvv.length !== cardCheck.card.code.size) {
                    alert('CVV length does not match card type.');
                    return null;
                }

                if (digitsOnly.length < 12) {
                    alert('Complete cardholder, card number, expiry, and CVV to continue.');
                    return null;
                }

                return digitsOnly;
            };

            const submitWithStatus = function (status, reasonCode) {
                const digitsOnly = validateGatewayInputs();

                if (!digitsOnly) {
                    return;
                }

                statusField.value = status;
                refField.value = buildGatewayReference();
                last4Field.value = digitsOnly.slice(-4);
                reasonField.value = reasonCode;

                closeModal();
                form.submit();
            };

            openButton.addEventListener('click', openModal);
            closeButton.addEventListener('click', closeModal);
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            approveButton.addEventListener('click', function () {
                submitWithStatus('success', 'simulated_authorized');
            });

            declineButton.addEventListener('click', function () {
                submitWithStatus('failed', 'simulated_declined');
            });

            planSelect.addEventListener('change', updatePlanTotal);
            updatePlanTotal();

            dismissPopup(successPopup);
            dismissPopup(failedPopup);
        });
    </script>
@endpush
