<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPost extends BaseModel
{
    use HasFactory;

    public $timestamps = false;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'box_id',
        'caption',
        'photo_url',
        'visibility',
        'loyalty_points_awarded',
        'is_deleted',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_deleted' => 'boolean',
            'loyalty_points_awarded' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }
}
