<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed canonical Bright-Market staff accounts.
     */
    public function run(): void
    {
        $passwordHash = Hash::make('POSDemo!2026');

        $users = [
            [
                'username' => 'admin.reyes',
                'full_name' => 'Carlos Reyes',
                'email' => 'admin.reyes@brightmarket.test',
                'email_verified_at' => now(),
                'password' => $passwordHash,
                'role' => 'Store Administrator',
                'avatar' => null,
                'created_at' => '2026-01-15 08:00:00',
                'updated_at' => '2026-01-15 08:00:00',
            ],
            [
                'username' => 'mgr.castro',
                'full_name' => 'Beatriz Castro',
                'email' => 'mgr.castro@brightmarket.test',
                'email_verified_at' => now(),
                'password' => $passwordHash,
                'role' => 'Shift Supervisor',
                'avatar' => null,
                'created_at' => '2026-01-20 08:30:00',
                'updated_at' => '2026-01-20 08:30:00',
            ],
            [
                'username' => 'cashier.valdez',
                'full_name' => 'Daniel Valdez',
                'email' => 'cashier.valdez@brightmarket.test',
                'email_verified_at' => now(),
                'password' => $passwordHash,
                'role' => 'Cashier',
                'avatar' => null,
                'created_at' => '2026-02-01 09:00:00',
                'updated_at' => '2026-02-01 09:00:00',
            ],
            [
                'username' => 'cashier.santos',
                'full_name' => 'Camille Santos',
                'email' => 'cashier.santos@brightmarket.test',
                'email_verified_at' => now(),
                'password' => $passwordHash,
                'role' => 'Cashier',
                'avatar' => null,
                'created_at' => '2026-02-01 09:15:00',
                'updated_at' => '2026-02-01 09:15:00',
            ],
            [
                'username' => 'inv.navarro',
                'full_name' => 'Leo Navarro',
                'email' => 'inv.navarro@brightmarket.test',
                'email_verified_at' => now(),
                'password' => $passwordHash,
                'role' => 'Inventory Clerk',
                'avatar' => null,
                'created_at' => '2026-02-10 10:00:00',
                'updated_at' => '2026-02-10 10:00:00',
            ],
        ];

        foreach ($users as $attributes) {
            User::query()->updateOrCreate(
                ['username' => $attributes['username']],
                $attributes
            );
        }
    }
}
