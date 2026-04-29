<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Str;

class BillingService
{
    public function __construct(
        private TaxService $taxService
    ) {}

    /**
     * @param  array{status?:string,gateway_ref?:string,gateway_reason_code?:string}  $gatewayPayload
     */
    public function charge(Subscription $subscription, string $reason, array $gatewayPayload = []): Payment
    {
        $subscription->loadMissing('plan');

        $subtotal = (float) $subscription->plan->price_monthly;
        $taxAmount = $this->taxService->calculate($subtotal);
        $status = $gatewayPayload['status'] ?? 'success';
        $gatewayReasonCode = $gatewayPayload['gateway_reason_code'] ?? $reason;

        return $subscription->payments()->create([
            'amount' => $subtotal + $taxAmount,
            'currency' => 'USD',
            'tax_amount' => $taxAmount,
            'status' => $status,
            'gateway_ref' => $gatewayPayload['gateway_ref'] ?? Str::upper(Str::random(12)),
            'gateway_reason_code' => substr($gatewayReasonCode, 0, 50),
            'retry_count' => $status === 'failed' ? 1 : 0,
            'next_retry_at' => $status === 'failed' ? now()->addHours(6) : null,
        ]);
    }
}
