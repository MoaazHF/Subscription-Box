<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'delivery_id',
        'type',
        'description',
        'photo_path',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Claim $model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** @return BelongsTo<Delivery, Claim> */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class, 'delivery_id');
    }
}
