<?php

namespace App\Services;

class TaxService
{
    public function calculate(float $subtotal): float
    {
        return round($subtotal * config('subscriptions.tax_rate', 0.10), 2);
    }
}
