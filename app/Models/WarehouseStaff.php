<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseStaff extends BaseModel
{
    use HasFactory;

    protected $table = 'warehouse_staff';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'warehouse_location',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
