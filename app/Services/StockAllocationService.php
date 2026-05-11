<?php

namespace App\Services;

use App\Models\Item;
use RuntimeException;

class StockAllocationService
{
    public function reserve(Item $item, int $quantity = 1): void
    {
        if ($quantity < 1) {
            throw new RuntimeException('Requested stock quantity must be positive.');
        }

        $updated = Item::query()
            ->whereKey($item->id)
            ->where('stock_qty', '>=', $quantity)
            ->decrement('stock_qty', $quantity);

        if ($updated === 0) {
            throw new RuntimeException('Insufficient stock for '.$item->name.'.');
        }

        $item->refresh();
    }

    public function release(Item $item, int $quantity = 1): void
    {
        if ($quantity < 1) {
            return;
        }

        Item::query()->whereKey($item->id)->increment('stock_qty', $quantity);
        $item->refresh();
    }
}
