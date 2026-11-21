<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Seed canonical sales ledger entries including a walk-in customer transaction.
     */
    public function run(): void
    {
        if (Sale::query()->exists()) {
            return;
        }

        $products = Product::query()->pluck('id', 'name');
        $customers = Customer::query()->pluck('id', 'email');
        $users = User::query()->pluck('id', 'username');

        $sales = [
            [
                'product_id' => $products['Artisan Cold Brew Bottle'],
                'customer_id' => $customers['teresa.mendoza@gmail.com'],
                'sold_by' => $users['cashier.valdez'],
                'quantity' => 2,
                'total_price' => '9.00',
                'created_at' => '2026-03-10 09:15:00',
                'updated_at' => '2026-03-10 09:15:00',
            ],
            [
                'product_id' => $products['Roasted Arabica Beans 250g'],
                'customer_id' => $customers['r.villanueva@outlook.ph'],
                'sold_by' => $users['admin.reyes'],
                'quantity' => 1,
                'total_price' => '14.50',
                'created_at' => '2026-03-10 10:40:00',
                'updated_at' => '2026-03-10 10:40:00',
            ],
            [
                'product_id' => $products['Sourdough Country Loaf'],
                'customer_id' => null,
                'sold_by' => $users['cashier.santos'],
                'quantity' => 3,
                'total_price' => '20.25',
                'created_at' => '2026-03-11 11:05:00',
                'updated_at' => '2026-03-11 11:05:00',
            ],
            [
                'product_id' => $products['Sea Salt Dark Chocolate Bar'],
                'customer_id' => $customers['clarissa.domingo@yahoo.com.ph'],
                'sold_by' => $users['cashier.valdez'],
                'quantity' => 4,
                'total_price' => '17.00',
                'created_at' => '2026-03-12 14:25:00',
                'updated_at' => '2026-03-12 14:25:00',
            ],
            [
                'product_id' => $products['Extra Virgin Olive Oil 500ml'],
                'customer_id' => $customers['lorna.bautista@outlook.ph'],
                'sold_by' => $users['admin.reyes'],
                'quantity' => 1,
                'total_price' => '16.90',
                'created_at' => '2026-03-13 16:50:00',
                'updated_at' => '2026-03-13 16:50:00',
            ],
        ];

        foreach ($sales as $sale) {
            Sale::query()->create($sale);
        }
    }
}
