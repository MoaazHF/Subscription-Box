<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Stub model for the `boxes` table owned by Team 2.
 * Only relationship and key configuration defined here.
 */
class Box extends Model
{
    protected $table = 'boxes';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    /** @return HasOne<Delivery> */
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class, 'box_id');
    }
}
