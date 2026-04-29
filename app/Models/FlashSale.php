<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSale extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'plan_id',
        'created_by',
        'name',
        'discount_percent',
        'stock_limit',
        'claimed_count',
        'start_at',
        'end_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'integer',
            'stock_limit' => 'integer',
            'claimed_count' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
