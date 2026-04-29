<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Delivery extends Model
{
    use HasFactory;

    public const PENDING = 'pending';

    public const PICKING = 'picking';

    public const PACKED = 'packed';

    public const SHIPPED = 'shipped';

    public const OUT_FOR_DELIVERY = 'out_for_delivery';

    public const DELIVERED = 'delivered';

    public const UNDELIVERABLE = 'undeliverable';

    public const STATUSES = [
        self::PENDING,
        self::PICKING,
        self::PACKED,
        self::SHIPPED,
        self::OUT_FOR_DELIVERY,
        self::DELIVERED,
        self::UNDELIVERABLE,
    ];

    public const STOPS_BY_STATUS = [
        self::PENDING => null,
        self::PICKING => 4,
        self::PACKED => 3,
        self::SHIPPED => 2,
        self::OUT_FOR_DELIVERY => 1,
        self::DELIVERED => 0,
        self::UNDELIVERABLE => 0,
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'box_id',
        'driver_id',
        'address_id',
        'status',
        'tracking_number',
        'estimated_delivery',
        'actual_delivery',
        'delivery_instructions',
        'stops_remaining',
        'eco_dispatch',
    ];

    protected function casts(): array
    {
        return [
            'estimated_delivery' => 'date',
            'actual_delivery' => 'datetime',
            'eco_dispatch' => 'boolean',
            'stops_remaining' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'delivery_id');
    }

    public function belongsToUser(User $user): bool
    {
        $this->loadMissing('address');

        return $this->address?->user_id === $user->id;
    }
}
