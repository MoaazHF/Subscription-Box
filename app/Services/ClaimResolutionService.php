<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\User;

class ClaimResolutionService
{
    public function resolve(Claim $claim, User $resolver, ?string $resolutionNotes = null): Claim
    {
        $claim->update([
            'status' => 'resolved',
            'resolved_by' => $resolver->id,
            'resolved_at' => now(),
            'resolution_notes' => $resolutionNotes,
        ]);

        return $claim->fresh(['delivery', 'subscription', 'resolvedBy']);
    }

    public function reject(Claim $claim, User $resolver, ?string $resolutionNotes = null): Claim
    {
        $claim->update([
            'status' => 'rejected',
            'resolved_by' => $resolver->id,
            'resolved_at' => now(),
            'resolution_notes' => $resolutionNotes,
        ]);

        return $claim->fresh(['delivery', 'subscription', 'resolvedBy']);
    }
}
