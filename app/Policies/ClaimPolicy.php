<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\Delivery;
use App\Models\User;

class ClaimPolicy
{
    public function create(User $user, Delivery $delivery): bool
    {
        return $user->isAdmin() || $delivery->belongsToUser($user);
    }

    public function update(User $user, Claim $claim): bool
    {
        return $user->isAdmin();
    }
}
