<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PosAuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_all_protected_pos_routes(): void
    {
        $protectedGetRoutes = [
            '/dashboard',
            '/products',
            '/products/create',
            '/customers',
            '/customers/create',
            '/users',
            '/users/create',
            '/sales',
            '/sales/create',
            '/profile',
            '/media/avatars/sample.jpg',
        ];

        foreach ($protectedGetRoutes as $uri) {
            $this->get($uri)->assertRedirect('/login');
        }

        $this->post('/products', [])->assertRedirect('/login');
        $this->post('/customers', [])->assertRedirect('/login');
        $this->post('/users', [])->assertRedirect('/login');
        $this->post('/sales', [])->assertRedirect('/login');
    }

    public function test_staff_can_sign_in_with_username_or_email(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/login', [
            'login' => 'admin.reyes',
            'password' => 'POSDemo!2026',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();

        $this->post('/login', [
            'login' => 'admin.reyes@brightmarket.test',
            'password' => 'POSDemo!2026',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_seeded_and_created_staff_passwords_are_securely_hashed(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('username', 'admin.reyes')->firstOrFail();
        $this->assertNotSame('POSDemo!2026', $admin->password);
        $this->assertTrue(Hash::check('POSDemo!2026', $admin->password));
    }
}
