<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Stub model for the `addresses` table owned by Team 1.
 * Only relationship and key configuration defined here.
 */
class Address extends Model
{
    protected $table = 'addresses';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    /** @return BelongsTo<User, Address> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return HasMany<Delivery> */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'address_id');
    }
}
