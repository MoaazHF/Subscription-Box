<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends BaseModel
{
    use HasFactory;

    public const QUEUED = 'queued';

    public const PROCESSING = 'processing';

    public const SENT = 'sent';

    public const FAILED = 'failed';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'type',
        'event_type',
        'subject',
        'body',
        'status',
        'idempotency_key',
        'retry_count',
        'last_error',
        'channel',
        'sent_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'processed_at' => 'datetime',
            'retry_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
