<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Seed canonical Bright-Market retail inventory items with curated product photos.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Artisan Cold Brew Bottle',
                'price' => '4.50',
                'stock_quantity' => 24,
                'image' => 'products/cold-brew.jpg',
                'created_at' => '2026-03-01 07:30:00',
                'updated_at' => '2026-03-01 07:30:00',
            ],
            [
                'name' => 'Sourdough Country Loaf',
                'price' => '6.75',
                'stock_quantity' => 14,
                'image' => 'products/sourdough.jpg',
                'created_at' => '2026-03-01 07:35:00',
                'updated_at' => '2026-03-01 07:35:00',
            ],
            [
                'name' => 'Organic Whole Milk 1L',
                'price' => '3.80',
                'stock_quantity' => 18,
                'image' => 'products/whole-milk.jpg',
                'created_at' => '2026-03-01 07:40:00',
                'updated_at' => '2026-03-01 07:40:00',
            ],
            [
                'name' => 'Roasted Arabica Beans 250g',
                'price' => '14.50',
                'stock_quantity' => 9,
                'image' => 'products/arabica-beans.jpg',
                'created_at' => '2026-03-02 08:00:00',
                'updated_at' => '2026-03-02 08:00:00',
            ],
            [
                'name' => 'Raw Wildflower Honey 350g',
                'price' => '11.25',
                'stock_quantity' => 3,
                'image' => 'products/wildflower-honey.jpg',
                'created_at' => '2026-03-02 08:10:00',
                'updated_at' => '2026-03-02 08:10:00',
            ],
            [
                'name' => 'Sea Salt Dark Chocolate Bar',
                'price' => '4.25',
                'stock_quantity' => 22,
                'image' => 'products/dark-chocolate.jpg',
                'created_at' => '2026-03-03 08:20:00',
                'updated_at' => '2026-03-03 08:20:00',
            ],
            [
                'name' => 'Extra Virgin Olive Oil 500ml',
                'price' => '16.90',
                'stock_quantity' => 5,
                'image' => 'products/olive-oil.jpg',
                'created_at' => '2026-03-03 08:30:00',
                'updated_at' => '2026-03-03 08:30:00',
            ],
            [
                'name' => 'Heirloom Tomato Crate 1kg',
                'price' => '5.40',
                'stock_quantity' => 0,
                'image' => 'products/heirloom-tomatoes.jpg',
                'created_at' => '2026-03-04 08:45:00',
                'updated_at' => '2026-03-04 08:45:00',
            ],
        ];

        foreach ($products as $attributes) {
            $relativePath = $attributes['image'];
            $sourceFile = database_path('seeders/media/'.$relativePath);

            if (is_file($sourceFile)) {
                Storage::disk('public')->put($relativePath, (string) file_get_contents($sourceFile));

                $thumbRel = 'products/thumbs/'.basename($relativePath);
                $thumbSource = database_path('seeders/media/'.$thumbRel);
                if (is_file($thumbSource)) {
                    Storage::disk('public')->put($thumbRel, (string) file_get_contents($thumbSource));
                }
            } else {
                $attributes['image'] = null;
            }

            Product::query()->updateOrCreate(
                ['name' => $attributes['name']],
                $attributes
            );
        }
    }
}
