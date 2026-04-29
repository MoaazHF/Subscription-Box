<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductCatalogService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): Item
    {
        $item = Item::query()->create($this->normalizePayload($payload));

        return $item->fresh();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(Item $item, array $payload): Item
    {
        if (($payload['remove_image'] ?? false) && $item->image_url) {
            $this->deleteImage($item->image_url);
            $payload['image_url'] = null;
        }

        $item->update($this->normalizePayload($payload, $item));

        return $item->fresh();
    }

    public function delete(Item $item): void
    {
        if ($item->image_url) {
            $this->deleteImage($item->image_url);
        }

        $item->delete();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizePayload(array $payload, ?Item $item = null): array
    {
        if (isset($payload['image']) && $payload['image'] instanceof UploadedFile) {
            if ($item?->image_url) {
                $this->deleteImage($item->image_url);
            }

            $payload['image_url'] = $payload['image']->store('products', 'public');
        }

        unset($payload['image'], $payload['remove_image']);

        if (isset($payload['origin_country']) && $payload['origin_country']) {
            $payload['origin_country'] = strtoupper((string) $payload['origin_country']);
        }

        $payload['is_limited_edition'] = (bool) ($payload['is_limited_edition'] ?? false);
        $payload['is_addon'] = (bool) ($payload['is_addon'] ?? false);
        $payload['limited_stock'] = $payload['is_limited_edition'] ? ($payload['limited_stock'] ?? null) : null;

        return $payload;
    }

    private function deleteImage(string $path): void
    {
        Storage::disk('public')->delete($path);
    }
}
