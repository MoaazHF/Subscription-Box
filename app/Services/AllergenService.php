<?php

namespace App\Services;

use App\Models\AllergenTag;
use App\Models\Item;
use App\Models\User;

class AllergenService
{
    /**
     * F18: Allergen conflict detection between a user and an item.
     *
     * @return array<string>
     */
    public function check(User $user, Item $item): array
    {
        $userTagIds = $user->allergenTags()->pluck('allergen_tags.id')->toArray();
        $itemTagIds = $item->allergenTags()->pluck('allergen_tags.id')->toArray();
        $conflicts = array_intersect($userTagIds, $itemTagIds);

        if (empty($conflicts)) {
            return [];
        }

        return AllergenTag::whereIn('id', $conflicts)->pluck('name')->toArray();
    }
}
