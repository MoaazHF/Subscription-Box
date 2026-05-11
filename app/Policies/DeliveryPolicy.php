<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;

class DeliveryPolicy
{
    public function view(User $user, Delivery $delivery): bool
    {
        return $user->isAdmin() || $delivery->belongsToUser($user);
    }

    public function updateStatus(User $user, Delivery $delivery): bool
    {
        return $user->isAdmin() && $delivery->status !== Delivery::DELIVERED;
    }

    public function updateByDriver(User $user, Delivery $delivery): bool
    {
        return $user->isDriver() && $user->driver?->id === $delivery->driver_id;
    }
}
