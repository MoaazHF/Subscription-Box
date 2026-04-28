<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Delivery extends Model
{
    use HasFactory;

    public const PENDING = 'pending';

    public const PACKED = 'packed';

    public const SHIPPED = 'shipped';

    public const OUT_FOR_DELIVERY = 'out_for_delivery';

    public const DELIVERED = 'delivered';

    public const UNDELIVERABLE = 'undeliverable';

    public const STATUSES = [
        self::PENDING,
        self::PACKED,
        self::SHIPPED,
        self::OUT_FOR_DELIVERY,
        self::DELIVERED,
        self::UNDELIVERABLE,
    ];

    public const STOPS_BY_STATUS = [
        self::PENDING => null,
        self::PACKED => 3,
        self::SHIPPED => 2,
        self::OUT_FOR_DELIVERY => 1,
        self::DELIVERED => 0,
        self::UNDELIVERABLE => 0,
    ];

    protected $table = 'deliveries';

    protected $primaryKey = 'id';

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

    protected $casts = [
        'estimated_delivery' => 'date',
        'actual_delivery' => 'datetime',
        'eco_dispatch' => 'boolean',
        'stops_remaining' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    public function driver()
    {
        // Assuming Driver model exists or will exist in Mohy's scope
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function belongsToUser(User $user): bool
    {
        $this->loadMissing('address');

        return $this->address?->user_id === $user->id;
    }

    public function claims()
    {
        // Assuming Claim model exists
        return $this->hasMany(Claim::class, 'delivery_id');
    }
}
