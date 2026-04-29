<?php

namespace App\Services;

use App\Models\Box;
use App\Models\SocialPost;
use App\Models\User;

class SocialPostService
{
    /** @param array{box_id:string,caption?:string,photo_url?:string,visibility?:string} $payload */
    public function create(User $user, array $payload): SocialPost
    {
        $box = Box::query()->findOrFail($payload['box_id']);

        abort_unless($box->ownedBy($user) || $user->isAdmin(), 403);

        return SocialPost::create([
            'user_id' => $user->id,
            'box_id' => $box->id,
            'caption' => $payload['caption'] ?? null,
            'photo_url' => $payload['photo_url'] ?? null,
            'visibility' => $payload['visibility'] ?? 'public',
            'loyalty_points_awarded' => 5,
            'is_deleted' => false,
            'created_at' => now(),
        ]);
    }

    public function softDelete(SocialPost $post, User $user): void
    {
        abort_unless($post->user_id === $user->id || $user->isAdmin(), 403);

        $post->update(['is_deleted' => true]);
    }
}
