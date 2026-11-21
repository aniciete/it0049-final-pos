<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Seed canonical Bright-Market customer records.
     */
    public function run(): void
    {
        $customers = [
            [
                'full_name' => 'Teresa Mendoza',
                'email' => 'teresa.mendoza@gmail.com',
                'phone' => '+63 917 482 1930',
                'created_at' => '2026-03-01 08:30:00',
                'updated_at' => '2026-03-01 08:30:00',
            ],
            [
                'full_name' => 'Rafael Villanueva',
                'email' => 'r.villanueva@outlook.ph',
                'phone' => '+63 918 634 7205',
                'created_at' => '2026-03-02 09:15:00',
                'updated_at' => '2026-03-02 09:15:00',
            ],
            [
                'full_name' => 'Clarissa Domingo',
                'email' => 'clarissa.domingo@yahoo.com.ph',
                'phone' => '+63 927 815 0492',
                'created_at' => '2026-03-03 10:45:00',
                'updated_at' => '2026-03-03 10:45:00',
            ],
            [
                'full_name' => 'Geronimo Pascual',
                'email' => 'gpascual.trade@gmail.com',
                'phone' => '+63 908 391 6584',
                'created_at' => '2026-03-04 14:20:00',
                'updated_at' => '2026-03-04 14:20:00',
            ],
            [
                'full_name' => 'Lorna Bautista',
                'email' => 'lorna.bautista@outlook.ph',
                'phone' => '+63 919 740 2816',
                'created_at' => '2026-03-05 16:05:00',
                'updated_at' => '2026-03-05 16:05:00',
            ],
        ];

        foreach ($customers as $attributes) {
            Customer::query()->updateOrCreate(
                ['email' => $attributes['email']],
                $attributes
            );
        }
    }
}
