<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftSubscription extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'purchaser_id',
        'recipient_user_id',
        'plan_id',
        'subscription_id',
        'recipient_email',
        'recipient_name',
        'duration_months',
        'activation_code',
        'status',
        'personal_message',
        'purchased_at',
        'activated_at',
        'scheduled_send_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'duration_months' => 'integer',
            'purchased_at' => 'datetime',
            'activated_at' => 'datetime',
            'scheduled_send_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchaser_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
