<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Notification;

class NotificationService
{
    public function notifyDeliveryStatus(Delivery $delivery): void
    {
        $delivery->loadMissing('box.subscription.user');

        $user = $delivery->box->subscription->user;
        $statusText = str_replace('_', ' ', $delivery->status);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'email',
            'event_type' => 'delivery_update',
            'subject' => 'Delivery Update: '.ucfirst($statusText),
            'body' => "Your subscription box delivery is now marked as {$statusText}.",
            'status' => 'queued',
        ]);
    }
}
