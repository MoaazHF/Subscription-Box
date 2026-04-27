<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Box extends BaseModel
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'subscription_id',
        'period_month',
        'period_year',
        'status',
        'lock_date',
        'theme',
        'total_weight_g',
        'shipping_tier',
    ];

    protected function casts(): array
    {
        return [
            'lock_date' => 'date',
            'period_month' => 'integer',
            'period_year' => 'integer',
            'total_weight_g' => 'integer',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'box_items');
    }

    public function customisation(): HasOne
    {
        return $this->hasOne(BoxCustomisation::class);
    }
}
