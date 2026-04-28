<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Notify user about their delivery status update.
     */
    public function notifyDeliveryStatus(Delivery $delivery): void
    {
        $user = $delivery->box->subscription->user;
        $statusStr = str_replace('_', ' ', $delivery->status);

        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'type' => 'delivery_update',
            'subject' => 'Delivery Update: '.ucfirst($statusStr),
            'body' => "Your subscription box delivery is now marked as {$statusStr}.",
            'status' => 'queued',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
