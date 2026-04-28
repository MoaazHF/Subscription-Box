<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class WarehouseStaff extends Model
{
    use HasFactory;

    protected $table = 'warehouse_staff';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'shift',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (WarehouseStaff $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** @return HasMany<InventoryLog> */
    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class, 'changed_by')
            ->where('changed_by_type', 'warehouse_staff');
    }
}
