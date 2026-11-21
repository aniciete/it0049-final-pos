<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with canonical Bright-Market POS data.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
            SaleSeeder::class,
        ]);
    }
}
