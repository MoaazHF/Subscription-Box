<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Str;

class BillingService
{
    public function __construct(
        private TaxService $taxService
    ) {
    }

    public function charge(Subscription $subscription, string $reason): Payment
    {
        $subscription->loadMissing('plan');

        $subtotal = (float) $subscription->plan->price_monthly;
        $taxAmount = $this->taxService->calculate($subtotal);

        return $subscription->payments()->create([
            'amount' => $subtotal + $taxAmount,
            'currency' => 'USD',
            'tax_amount' => $taxAmount,
            'status' => 'success',
            'gateway_ref' => Str::upper(Str::random(12)),
            'gateway_reason_code' => $reason,
            'retry_count' => 0,
        ]);
    }
}
