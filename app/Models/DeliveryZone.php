<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryZone extends BaseModel
{
    use HasFactory;

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'region',
        'country',
        'is_serviceable',
    ];

    protected function casts(): array
    {
        return [
            'is_serviceable' => 'boolean',
        ];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
