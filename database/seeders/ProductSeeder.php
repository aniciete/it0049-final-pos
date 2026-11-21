<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed canonical Bright-Market retail inventory items.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Artisan Cold Brew Bottle',
                'price' => '4.50',
                'stock_quantity' => 24,
                'image' => null,
                'created_at' => '2026-03-01 07:30:00',
                'updated_at' => '2026-03-01 07:30:00',
            ],
            [
                'name' => 'Sourdough Country Loaf',
                'price' => '6.75',
                'stock_quantity' => 14,
                'image' => null,
                'created_at' => '2026-03-01 07:35:00',
                'updated_at' => '2026-03-01 07:35:00',
            ],
            [
                'name' => 'Organic Whole Milk 1L',
                'price' => '3.80',
                'stock_quantity' => 18,
                'image' => null,
                'created_at' => '2026-03-01 07:40:00',
                'updated_at' => '2026-03-01 07:40:00',
            ],
            [
                'name' => 'Roasted Arabica Beans 250g',
                'price' => '14.50',
                'stock_quantity' => 9,
                'image' => null,
                'created_at' => '2026-03-02 08:00:00',
                'updated_at' => '2026-03-02 08:00:00',
            ],
            [
                'name' => 'Raw Wildflower Honey 350g',
                'price' => '11.25',
                'stock_quantity' => 3,
                'image' => null,
                'created_at' => '2026-03-02 08:10:00',
                'updated_at' => '2026-03-02 08:10:00',
            ],
            [
                'name' => 'Sea Salt Dark Chocolate Bar',
                'price' => '4.25',
                'stock_quantity' => 22,
                'image' => null,
                'created_at' => '2026-03-03 08:20:00',
                'updated_at' => '2026-03-03 08:20:00',
            ],
            [
                'name' => 'Extra Virgin Olive Oil 500ml',
                'price' => '16.90',
                'stock_quantity' => 5,
                'image' => null,
                'created_at' => '2026-03-03 08:30:00',
                'updated_at' => '2026-03-03 08:30:00',
            ],
            [
                'name' => 'Heirloom Tomato Crate 1kg',
                'price' => '5.40',
                'stock_quantity' => 0,
                'image' => null,
                'created_at' => '2026-03-04 08:45:00',
                'updated_at' => '2026-03-04 08:45:00',
            ],
        ];

        foreach ($products as $attributes) {
            Product::query()->updateOrCreate(
                ['name' => $attributes['name']],
                $attributes
            );
        }
    }
}
