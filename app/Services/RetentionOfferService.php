<?php

namespace App\Services;

use App\Models\RetentionOffer;
use App\Models\Subscription;

class RetentionOfferService
{
    /** @param array{offer_type:string,offer_value:string,cancellation_reason?:string} $payload */
    public function present(Subscription $subscription, array $payload): RetentionOffer
    {
        return RetentionOffer::create([
            'subscription_id' => $subscription->id,
            'offer_type' => $payload['offer_type'],
            'offer_value' => $payload['offer_value'],
            'cancellation_reason' => $payload['cancellation_reason'] ?? null,
            'presented_at' => now(),
            'accepted' => false,
            'accepted_at' => null,
        ]);
    }

    public function updateDecision(RetentionOffer $offer, bool $accepted): RetentionOffer
    {
        $offer->update([
            'accepted' => $accepted,
            'accepted_at' => $accepted ? now() : null,
        ]);

        return $offer->fresh();
    }
}
