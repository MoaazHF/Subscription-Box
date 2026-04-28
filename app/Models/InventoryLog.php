<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InventoryLog extends Model
{
    use HasFactory;

    protected $table = 'inventory_logs';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'delivery_id',
        'event',
        'from_value',
        'to_value',
        'changed_by',
        'changed_by_type',
        'notes',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (InventoryLog $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** @return BelongsTo<Delivery, InventoryLog> */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }

    /**
     * Convenience factory method: log a status change for a delivery.
     */
    public static function logStatusChange(
        Delivery $delivery,
        string $from,
        string $to,
        ?string $changedBy = null,
        string $changedByType = 'user'
    ): static {
        return static::create([
            'delivery_id' => $delivery->id,
            'event' => 'status_changed',
            'from_value' => $from,
            'to_value' => $to,
            'changed_by' => $changedBy,
            'changed_by_type' => $changedByType,
        ]);
    }

    /**
     * Convenience factory method: log a claim being filed.
     */
    public static function logClaimFiled(Delivery $delivery, string $claimType, ?string $changedBy = null): static
    {
        return static::create([
            'delivery_id' => $delivery->id,
            'event' => 'claim_filed',
            'from_value' => null,
            'to_value' => $claimType,
            'changed_by' => $changedBy,
            'changed_by_type' => 'user',
            'notes' => "Claim type: {$claimType}",
        ]);
    }
}
