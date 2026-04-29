<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PromoCodeService
{
    /** @param array{code:string,discount_type:string,discount_value:float,max_uses?:int,expires_at?:string} $payload */
    public function create(User $creator, array $payload): PromoCode
    {
        return PromoCode::create([
            'created_by' => $creator->id,
            'code' => strtoupper($payload['code']),
            'discount_type' => $payload['discount_type'],
            'discount_value' => $payload['discount_value'],
            'max_uses' => $payload['max_uses'] ?? null,
            'used_count' => 0,
            'expires_at' => $payload['expires_at'] ?? null,
            'created_at' => now(),
        ]);
    }

    public function applyForUser(PromoCode $promoCode, User $user): PromoCodeUsage
    {
        abort_if($promoCode->expires_at && $promoCode->expires_at->isPast(), 422, 'Promo code expired.');

        if ($promoCode->max_uses !== null) {
            abort_if($promoCode->used_count >= $promoCode->max_uses, 422, 'Promo code usage limit reached.');
        }

        return DB::transaction(function () use ($promoCode, $user): PromoCodeUsage {
            $existing = PromoCodeUsage::query()
                ->where('promo_code_id', $promoCode->id)
                ->where('user_id', $user->id)
                ->first();

            abort_if($existing !== null, 422, 'Promo code already used by this user.');

            $usage = PromoCodeUsage::create([
                'promo_code_id' => $promoCode->id,
                'user_id' => $user->id,
                'used_at' => now(),
            ]);

            $promoCode->increment('used_count');

            return $usage;
        });
    }
}
