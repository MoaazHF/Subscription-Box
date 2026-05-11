<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicStorageFallbackTest extends TestCase
{
    public function test_it_serves_existing_public_storage_files_via_media_route(): void
    {
        Storage::disk('public')->put('products/fallback-test.txt', 'image-bytes');

        $this->get(route('media.show', ['path' => 'products/fallback-test.txt']))
            ->assertOk()
            ->assertStreamedContent('image-bytes');
    }

    public function test_it_returns_not_found_for_missing_public_storage_files(): void
    {
        $this->get(route('media.show', ['path' => 'products/not-found.jpg']))
            ->assertNotFound();
    }

    public function test_it_rejects_paths_outside_allowed_folders(): void
    {
        Storage::disk('public')->put('private/secret.txt', 'hidden');

        $this->get(route('media.show', ['path' => 'private/secret.txt']))
            ->assertNotFound();
    }

    public function test_it_serves_allowed_branding_assets(): void
    {
        $this->get(route('media.branding', ['file' => 'basic.png']))
            ->assertOk();
    }
}
