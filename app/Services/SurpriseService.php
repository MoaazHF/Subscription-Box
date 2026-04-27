<?php

namespace App\Services;

use App\Models\Box;
use App\Models\Item;
use App\Models\User;

class SurpriseService
{
    /**
     * F14: Pick a random allergen-safe item not already in the box.
     */
    public function pickSurprise(Box $box, User $user): ?Item
    {
        $userAllergenIds = $user->allergenTags()->pluck('allergen_tags.id');
        $boxItemIds = $box->items()->pluck('items.id');

        return Item::whereNotIn('id', $boxItemIds)
            ->whereDoesntHave('allergenTags', fn ($q) => $q->whereIn('allergen_tags.id', $userAllergenIds))
            ->inRandomOrder()
            ->first();
    }
}
