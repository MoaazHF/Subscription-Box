<?php

namespace App\Services;

use App\Models\BoxItem;

class DuplicatePreventionService
{
    /**
     * F23: Check if an item appeared in the subscriber's last 3 delivered/shipped/packed boxes.
     */
    public function wouldBeDuplicate(string $userId, string $itemId): bool
    {
        $recentItemIds = BoxItem::whereHas('box', function ($query) use ($userId) {
            $query->whereHas('subscription', fn ($s) => $s->where('user_id', $userId))
                ->whereIn('status', ['delivered', 'shipped', 'packed'])
                ->orderByDesc('period_year')
                ->orderByDesc('period_month')
                ->limit(3);
        })->pluck('item_id')->unique()->toArray();

        return in_array($itemId, $recentItemIds);
    }
}
