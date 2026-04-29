<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductCrudTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_update_and_delete_product_with_image(): void
    {
        $this->seed();
        Storage::fake('public');

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $createResponse = $this->actingAs($admin)->post(route('products.store'), [
            'name' => 'Test Product',
            'description' => 'Catalog product description',
            'weight_g' => 400,
            'size_category' => 'medium',
            'unit_price' => 12.50,
            'stock_qty' => 20,
            'is_limited_edition' => 1,
            'limited_stock' => 20,
            'supplier' => 'Demo Supplier',
            'origin_country' => 'EG',
            'sourcing_notes' => 'Handled by QA',
            'is_addon' => 1,
            'image' => UploadedFile::fake()->image('product.png'),
        ]);

        $createResponse->assertSessionHas('status');

        $product = Item::query()->where('name', 'Test Product')->firstOrFail();
        $this->assertNotNull($product->image_url);
        Storage::disk('public')->assertExists($product->image_url);

        $updateResponse = $this->actingAs($admin)->put(route('products.update', $product), [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'weight_g' => 450,
            'size_category' => 'large',
            'unit_price' => 15.75,
            'stock_qty' => 18,
            'is_limited_edition' => 0,
            'supplier' => 'New Supplier',
            'origin_country' => 'US',
            'sourcing_notes' => 'Updated note',
            'is_addon' => 0,
            'remove_image' => 1,
        ]);

        $updateResponse->assertSessionHas('status');

        $this->assertDatabaseHas('items', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'size_category' => 'large',
            'origin_country' => 'US',
            'image_url' => null,
        ]);

        $deleteResponse = $this->actingAs($admin)->delete(route('products.destroy', $product));

        $deleteResponse->assertSessionHas('status');

        $this->assertDatabaseMissing('items', [
            'id' => $product->id,
        ]);
    }
}
