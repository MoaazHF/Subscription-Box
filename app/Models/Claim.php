<?php

namespace App\Models;

class Claim extends BaseModel
{
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'subscription_id',
        'delivery_id',
        'item_id',
        'type',
        'description',
        'photo_url',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
