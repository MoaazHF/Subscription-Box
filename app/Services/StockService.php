<?php

namespace App\Services;

use App\Models\Box;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockService
{
    /**
     * F17: Atomically claim a limited-edition item using PostgreSQL row-level lock.
     *
     * @return array{ok: bool, error?: string}
     */
    public function claimLimited(Box $box, Item $item): array
    {
        if (! $item->is_limited_edition) {
            return ['ok' => false, 'error' => 'Not a limited edition item'];
        }

        return DB::transaction(function () use ($box, $item) {
            $locked = Item::lockForUpdate()->find($item->id);

            if ($locked->limited_stock !== null && $locked->limited_stock <= 0) {
                return ['ok' => false, 'error' => 'Sold Out'];
            }

            if ($locked->limited_stock !== null) {
                $locked->decrement('limited_stock');
            }

            $locked->decrement('stock_qty');

            $box->boxItems()->create([
                'id' => (string) Str::uuid(),
                'item_id' => $locked->id,
                'quantity' => 1,
                'is_surprise' => false,
                'added_at' => now(),
            ]);

            return ['ok' => true];
        });
    }
}
