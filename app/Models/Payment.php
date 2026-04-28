<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends BaseModel
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'subscription_id',
        'amount',
        'currency',
        'tax_amount',
        'status',
        'gateway_ref',
        'gateway_reason_code',
        'retry_count',
        'next_retry_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'retry_count' => 'integer',
        'next_retry_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
