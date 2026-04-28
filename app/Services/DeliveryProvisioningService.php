<?php

namespace App\Services;

use App\Models\Box;
use App\Models\Delivery;
use App\Models\Subscription;
use Illuminate\Support\Str;

class DeliveryProvisioningService
{
    public function provisionForBox(Box $box, Subscription $subscription): void
    {
        Delivery::query()->firstOrCreate(
            [
                'box_id' => $box->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'address_id' => $subscription->address_id,
                'status' => Delivery::PENDING,
                'tracking_number' => 'TRK-'.Str::upper(Str::random(10)),
                'estimated_delivery' => now()->addDays($subscription->eco_shipping ? 5 : 3)->toDateString(),
                'actual_delivery' => null,
                'delivery_instructions' => 'Leave at the door if no answer.',
                'stops_remaining' => Delivery::STOPS_BY_STATUS[Delivery::PENDING],
                'eco_dispatch' => $subscription->eco_shipping,
            ]
        );
    }
}
