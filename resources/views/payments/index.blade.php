@extends('layouts.app')

@section('content')
    <section class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Billing basics</p>
        <h1 class="mt-3 text-3xl font-black text-stone-900">Payment history</h1>
        <p class="mt-2 text-sm leading-7 text-stone-600">Payments are simulated, but the records are real. Each subscription creation, plan switch, and renewal writes a row here.</p>

        <div class="mt-8 overflow-hidden rounded-[1.5rem] border border-stone-200">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-100">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Plan</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Reason</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Tax</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Amount</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Status</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="px-4 py-4 text-sm font-semibold text-stone-900">{{ ucfirst($payment->subscription->plan?->name ?? 'Plan') }}</td>
                            <td class="px-4 py-4 text-sm text-stone-600">{{ ucwords(str_replace('_', ' ', $payment->gateway_reason_code)) }}</td>
                            <td class="px-4 py-4 text-sm text-stone-600">${{ number_format((float) $payment->tax_amount, 2) }}</td>
                            <td class="px-4 py-4 text-sm font-semibold text-stone-900">${{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-4 py-4 text-sm text-stone-600">{{ ucfirst($payment->status) }}</td>
                            <td class="px-4 py-4 text-sm text-stone-600">{{ $payment->created_at?->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-sm text-stone-600">No payment records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    </section>
@endsection
