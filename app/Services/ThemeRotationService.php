<?php

namespace App\Services;

use App\Models\Box;
use Illuminate\Support\Facades\DB;

class ThemeRotationService
{
    /**
     * Check if the given item was present in the subscription's previous period box as a surprise.
     */
    public function wasInPreviousBox(Box $currentBox, string $itemId): bool
    {
        $prevMonth = $currentBox->period_month == 1 ? 12 : $currentBox->period_month - 1;
        $prevYear = $currentBox->period_month == 1 ? $currentBox->period_year - 1 : $currentBox->period_year;

        $previousBox = Box::where('subscription_id', $currentBox->subscription_id)
            ->where('period_month', $prevMonth)
            ->where('period_year', $prevYear)
            ->first();

        if (! $previousBox) {
            return false;
        }

        return DB::table('box_items')
            ->where('box_id', $previousBox->id)
            ->where('item_id', $itemId)
            ->where('is_surprise', true)
            ->exists();
    }
}
