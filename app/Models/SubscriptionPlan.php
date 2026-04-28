<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends BaseModel
{
    use HasFactory;

    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'price_monthly',
        'max_items',
        'max_weight_g',
        'features',
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationship: A plan has many subscriptions
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}