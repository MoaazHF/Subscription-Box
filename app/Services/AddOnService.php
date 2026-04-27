<?php

namespace App\Services;

use App\Models\Box;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Str;

class AddOnService
{
    /**
     * F16: Add an add-on item to an open box (no shipping surcharge).
     *
     * @return array{ok: bool, error?: string}
     */
    public function add(Box $box, Item $item, User $user): array
    {
        if ($box->status !== 'open' || now()->greaterThan($box->lock_date)) {
            return ['ok' => false, 'error' => 'Box is locked'];
        }

        if (! $item->is_addon) {
            return ['ok' => false, 'error' => 'Item is not an add-on'];
        }

        $box->boxItems()->create([
            'id' => (string) Str::uuid(),
            'item_id' => $item->id,
            'quantity' => 1,
            'is_addon' => true,
            'added_at' => now(),
        ]);

        return ['ok' => true];
    }
}
