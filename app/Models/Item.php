<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

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
        'is_addon',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'is_limited_edition' => 'boolean',
            'is_addon' => 'boolean',
            'unit_price' => 'decimal:2',
            'stock_qty' => 'integer',
            'weight_g' => 'integer',
            'limited_stock' => 'integer',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model): void {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function allergenTags(): BelongsToMany
    {
        return $this->belongsToMany(AllergenTag::class, 'item_allergens', 'item_id', 'allergen_tag_id');
    }

    public function boxItems(): HasMany
    {
        return $this->hasMany(BoxItem::class);
    }
}
