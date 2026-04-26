<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'weight_g',
        'size_category',
        'unit_price',
        'stock_qty',
        'is_limited_edition',
        'limited_stock',
        'supplier',
        'origin_country',
        'sourcing_notes',
    ];

    protected function casts(): array
    {
        return [
            'weight_g' => 'integer',
            'unit_price' => 'decimal:2',
            'stock_qty' => 'integer',
            'is_limited_edition' => 'boolean',
            'limited_stock' => 'integer',
        ];
    }

    public function boxes(): BelongsToMany
    {
        return $this->belongsToMany(Box::class, 'box_items');
    }
}
