<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends BaseModel
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'plan_id',
        'address_id',
        'status',
        'start_date',
        'next_billing_date',
        'remaining_billing_days',
        'auto_renew',
        'eco_shipping',
        'loyalty_points',
    ];

    protected function casts(): array
    {
        return [
            'plan_id' => 'integer',
            'start_date' => 'date',
            'next_billing_date' => 'date',
            'remaining_billing_days' => 'integer',
            'auto_renew' => 'boolean',
            'eco_shipping' => 'boolean',
            'loyalty_points' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function boxes(): HasMany
    {
        return $this->hasMany(Box::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function retentionOffers(): HasMany
    {
        return $this->hasMany(RetentionOffer::class);
    }

    public function giftSubscription(): HasOne
    {
        return $this->hasOne(GiftSubscription::class);
    }
}
