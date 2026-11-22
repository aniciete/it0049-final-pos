<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_rendered_views_contain_zero_eyebrows_and_zero_em_dashes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $staff = User::query()->where('username', 'admin.reyes')->firstOrFail();
        $product = Product::query()->firstOrFail();
        $customer = Customer::query()->firstOrFail();
        $sale = Sale::query()->firstOrFail();

        $guestUris = [
            route('home'),
            route('about'),
            route('login'),
            route('register'),
        ];

        foreach ($guestUris as $uri) {
            $response = $this->get($uri)->assertOk();
            $this->assertCleanUiHtml($response->getContent(), $uri);
        }

        $authUris = [
            route('dashboard'),
            route('products.index'),
            route('products.index', ['view' => 'archived']),
            route('products.create'),
            route('products.show', $product),
            route('products.edit', $product),
            route('customers.index'),
            route('customers.create'),
            route('customers.show', $customer),
            route('customers.edit', $customer),
            route('users.index'),
            route('users.create'),
            route('users.show', $staff),
            route('users.edit', $staff),
            route('sales.index'),
            route('sales.create'),
            route('sales.show', $sale),
            route('profile.edit'),
        ];

        foreach ($authUris as $uri) {
            $response = $this->actingAs($staff)->get($uri)->assertOk();
            $this->assertCleanUiHtml($response->getContent(), $uri);
        }
    }

    public function test_login_demo_hint_is_gated_by_config_and_home_hides_revenue_for_guests(): void
    {
        config(['app.demo_hint' => false]);
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('POSDemo!2026')
            ->assertDontSee('Fill Demo Credentials');

        config(['app.demo_hint' => true]);
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Fill Demo Credentials');

        $product = Product::query()->create(['name' => 'Counter Roast', 'price' => '12.34', 'stock_quantity' => 5]);
        $staff = User::factory()->create();
        Sale::query()->create([
            'product_id' => $product->id,
            'customer_id' => null,
            'sold_by' => $staff->id,
            'quantity' => 1,
            'total_price' => '12.34',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('$12.34')
            ->assertDontSee('gross');
    }

    public function test_guest_navigation_hides_internal_routes_and_auth_renders_auth_shell(): void
    {
        foreach ([route('home'), route('about')] as $uri) {
            $html = $this->get($uri)->assertOk()->getContent();
            $headerEnd = strpos($html, '</header>');
            $headerHtml = $headerEnd !== false ? substr($html, 0, $headerEnd) : $html;

            $this->assertStringNotContainsString(route('products.index'), $headerHtml);
            $this->assertStringNotContainsString(route('sales.create'), $headerHtml);
            $this->assertStringNotContainsString(route('sales.index'), $headerHtml);
            $this->assertStringNotContainsString(route('customers.index'), $headerHtml);
            $this->assertStringNotContainsString(route('users.index'), $headerHtml);
            $this->assertStringNotContainsString(route('dashboard'), $headerHtml);
        }

        $loginHtml = $this->get(route('login'))->assertOk()->getContent();
        $this->assertStringContainsString('class="auth-shell"', $loginHtml);
        $this->assertStringNotContainsString('>Overview<', $loginHtml);
    }

    private function assertCleanUiHtml(string $html, string $uri): void
    {
        $this->assertStringNotContainsString('class="eyebrow"', $html, "Unexpected eyebrow on {$uri}");
        $this->assertStringNotContainsString("\u{2014}", $html, "Unexpected literal em-dash on {$uri}");
        $this->assertStringNotContainsString('public disk', $html, "Unexpected public disk jargon on {$uri}");
        $this->assertStringNotContainsString('Bcrypt Hashed', $html, "Unexpected Bcrypt Hashed badge on {$uri}");
        $this->assertStringNotContainsString('atomically', $html, "Unexpected atomically copy on {$uri}");
    }
}
