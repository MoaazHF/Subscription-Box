<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLogService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(
        ?User $user,
        string $action,
        ?Model $entity = null,
        array $metadata = [],
        ?string $ipAddress = null
    ): void {
        $entityKey = $entity?->getKey();

        if ($entityKey !== null && ! Str::isUuid((string) $entityKey)) {
            $metadata['entity_key'] = $entityKey;
            $entityKey = null;
        }

        AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => $entity ? class_basename($entity) : null,
            'entity_id' => $entityKey,
            'ip_address' => $ipAddress,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
