<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Tests\TestCase;

class PosProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_staff_can_create_product_with_resized_image(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create();

        $upload = UploadedFile::fake()->image('cold-brew.png', 1000, 800);

        $response = $this->actingAs($staff)->post(route('products.store'), [
            'name' => 'Matcha Oat Latte',
            'price' => '5.50',
            'stock_quantity' => 15,
            'image' => $upload,
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::query()->where('name', 'Matcha Oat Latte')->firstOrFail();
        $this->assertSame('5.50', $product->price);
        $this->assertSame(15, $product->stock_quantity);
        $this->assertNotNull($product->image);
        $this->assertStringStartsWith('products/', $product->image);

        Storage::disk('public')->assertExists($product->image);
        $thumbPath = ImageUploadService::thumbnailRelativePath($product->image);
        $this->assertNotNull($thumbPath);
        Storage::disk('public')->assertExists($thumbPath);

        $decoded = Image::decode(Storage::disk('public')->get($product->image));
        $this->assertSame(640, $decoded->width());
        $this->assertSame(640, $decoded->height());

        $decodedThumb = Image::decode(Storage::disk('public')->get($thumbPath));
        $this->assertSame(96, $decodedThumb->width());
        $this->assertSame(96, $decodedThumb->height());

        $this->actingAs($staff)
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('Matcha Oat Latte')
            ->assertSee(basename($product->image));

        $mediaResponse = $this->actingAs($staff)
            ->get(route('media.thumb', ['folder' => 'products', 'filename' => basename($product->image)]))
            ->assertOk();
        $etag = $mediaResponse->headers->get('ETag');
        $this->assertNotEmpty($etag);
        $this->assertStringContainsString('max-age=', (string) $mediaResponse->headers->get('Cache-Control'));

        $this->actingAs($staff)
            ->withHeaders(['If-None-Match' => $etag])
            ->get(route('media.thumb', ['folder' => 'products', 'filename' => basename($product->image)]))
            ->assertStatus(304);

        $this->actingAs($staff)
            ->get('/media/products/thumbs')
            ->assertNotFound();
    }

    public function test_product_validation_rejects_invalid_image_and_retains_old_input(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create();

        $badFile = UploadedFile::fake()->create('script.txt', 10, 'text/plain');

        $response = $this->actingAs($staff)
            ->from(route('products.create'))
            ->post(route('products.store'), [
                'name' => 'Invalid Product Test',
                'price' => '-5.00',
                'stock_quantity' => -1,
                'image' => $badFile,
            ]);

        $response->assertRedirect(route('products.create'));
        $response->assertSessionHasErrors(['price', 'stock_quantity', 'image']);
        $this->assertSame('Invalid Product Test', session()->getOldInput('name'));
        $this->assertDatabaseMissing('products', ['name' => 'Invalid Product Test']);
    }

    public function test_updating_product_image_cleans_up_previous_file(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create();

        $firstFile = UploadedFile::fake()->image('first.jpg', 800, 800);
        $this->actingAs($staff)->post(route('products.store'), [
            'name' => 'Brioche Bun',
            'price' => '3.25',
            'stock_quantity' => 10,
            'image' => $firstFile,
        ]);

        $product = Product::query()->where('name', 'Brioche Bun')->firstOrFail();
        $firstPath = $product->image;
        Storage::disk('public')->assertExists($firstPath);

        $secondFile = UploadedFile::fake()->image('second.webp', 800, 800);
        $this->actingAs($staff)->put(route('products.update', $product), [
            'name' => 'Brioche Bun Deluxe',
            'price' => '3.75',
            'stock_quantity' => 12,
            'image' => $secondFile,
        ])->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertNotSame($firstPath, $product->image);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertMissing(ImageUploadService::thumbnailRelativePath($firstPath));
        Storage::disk('public')->assertExists($product->image);
        Storage::disk('public')->assertExists(ImageUploadService::thumbnailRelativePath($product->image));
    }

    public function test_products_can_be_archived_restored_and_guarded_against_purge_when_sold(): void
    {
        $staff = User::factory()->create();

        $unsold = Product::query()->create([
            'name' => 'Temporary Tart',
            'price' => '4.00',
            'stock_quantity' => 5,
        ]);

        $soldProduct = Product::query()->create([
            'name' => 'Signature Espresso',
            'price' => '4.50',
            'stock_quantity' => 10,
        ]);

        Sale::query()->create([
            'product_id' => $soldProduct->id,
            'customer_id' => null,
            'sold_by' => $staff->id,
            'quantity' => 1,
            'total_price' => '4.50',
        ]);

        // Archive both
        $this->actingAs($staff)->delete(route('products.destroy', $unsold->id))->assertRedirect(route('products.index'));
        $this->actingAs($staff)->delete(route('products.destroy', $soldProduct->id))->assertRedirect(route('products.index'));

        $this->assertSoftDeleted('products', ['id' => $unsold->id]);
        $this->assertSoftDeleted('products', ['id' => $soldProduct->id]);

        // Purging sold product is blocked
        $this->actingAs($staff)
            ->from(route('products.index', ['view' => 'archived']))
            ->delete(route('products.destroy', [$soldProduct->id, 'purge' => 1]))
            ->assertRedirect(route('products.index', ['view' => 'archived']))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $soldProduct->id]);

        // Restore sold product & permanently purge unsold product
        $this->actingAs($staff)->post(route('products.restore', $soldProduct->id))->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', ['id' => $soldProduct->id, 'deleted_at' => null]);

        $this->actingAs($staff)
            ->delete(route('products.destroy', [$unsold->id, 'purge' => 1]))
            ->assertRedirect(route('products.index', ['view' => 'archived']));

        $this->assertDatabaseMissing('products', ['id' => $unsold->id]);
    }
}
