<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BoxCustomisation extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'box_id',
        'swap_allowed',
        'theme_preference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'swap_allowed' => 'boolean',
        ];
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }
}
