<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxItem extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'box_id',
        'item_id',
        'quantity',
        'is_addon',
        'is_swapped',
        'is_surprise',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'is_addon' => 'boolean',
            'is_swapped' => 'boolean',
            'is_surprise' => 'boolean',
        ];
    }
}
