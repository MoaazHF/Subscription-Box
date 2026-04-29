<?php

namespace App\Services;

use App\Models\FlashSale;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FlashSaleService
{
    public function claim(FlashSale $flashSale, User $user): void
    {
        abort_unless($flashSale->start_at <= now() && $flashSale->end_at >= now(), 422, 'Flash sale not active.');

        if ($flashSale->stock_limit !== null) {
            abort_if($flashSale->claimed_count >= $flashSale->stock_limit, 422, 'Flash sale stock exhausted.');
        }

        DB::transaction(function () use ($flashSale, $user): void {
            $flashSale->increment('claimed_count');

            $user->rewards()->create([
                'type' => 'account_credit',
                'amount' => round((float) $flashSale->plan?->price_monthly * ($flashSale->discount_percent / 100), 2),
                'points' => null,
                'description' => "Flash sale claim: {$flashSale->name}",
                'is_applied' => false,
                'created_at' => now(),
            ]);
        });
    }
}
