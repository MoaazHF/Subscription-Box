<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'phone',
        'vehicle_plate',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Driver $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** @return HasMany<Delivery> */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'driver_id');
    }
}
