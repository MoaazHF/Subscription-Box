<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Delivery extends Model
{
    use HasFactory;

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

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimated_delivery' => 'date',
            'actual_delivery' => 'datetime',
            'eco_dispatch' => 'boolean',
            'stops_remaining' => 'integer',
        ];
    }

    /**
     * Valid status transitions for the delivery state machine.
     *
     * @var array<string, string[]>
     */
    public const STATUS_TRANSITIONS = [
        'pending' => ['picking'],
        'picking' => ['packed'],
        'packed' => ['shipped'],
        'shipped' => ['out_for_delivery'],
        'out_for_delivery' => ['delivered', 'undeliverable'],
        'delivered' => [],
        'undeliverable' => [],
    ];

    public const ALL_STATUSES = [
        'pending',
        'picking',
        'packed',
        'shipped',
        'out_for_delivery',
        'delivered',
        'undeliverable',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Delivery $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Determine if this delivery can transition to the given status.
     */
    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::STATUS_TRANSITIONS[$this->status] ?? [], true);
    }

    /**
     * Scope deliveries to those belonging to the given user via their address.
     *
     * @param  Builder<Delivery>  $query
     */
    public function scopeForUser(Builder $query, int|string $userId): Builder
    {
        return $query->whereHas('address', fn (Builder $q) => $q->where('user_id', $userId));
    }

    /** @return BelongsTo<Box, Delivery> */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class, 'box_id');
    }

    /** @return BelongsTo<Driver, Delivery> */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /** @return BelongsTo<Address, Delivery> */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /** @return HasMany<Claim> */
    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'delivery_id');
    }
}
