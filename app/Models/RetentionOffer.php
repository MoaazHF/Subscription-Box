<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetentionOffer extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'subscription_id',
        'offer_type',
        'offer_value',
        'cancellation_reason',
        'presented_at',
        'accepted',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'presented_at' => 'datetime',
            'accepted' => 'boolean',
            'accepted_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
