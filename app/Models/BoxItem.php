<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoxItem extends BaseModel
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'box_id',
        'item_id',
        'bundle_id',
        'quantity',
        'is_swapped',
        'is_addon',
        'is_surprise',
        'added_at',
    ];

    protected function casts(): array
    {
        return [
            'is_addon' => 'boolean',
            'is_surprise' => 'boolean',
            'is_swapped' => 'boolean',
            'added_at' => 'datetime',
            'quantity' => 'integer',
        ];
    }

    /** @return BelongsTo<Box, BoxItem> */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    /** @return BelongsTo<Item, BoxItem> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }
}
