<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosSalesTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_recording_sale_calculates_total_server_side_and_deducts_stock_atomically(): void
    {
        $staff = User::factory()->create(['full_name' => 'Daniel Valdez', 'username' => 'cashier.valdez']);
        $customer = Customer::query()->create([
            'full_name' => 'Teresa Mendoza',
            'email' => 'teresa@example.com',
        ]);
        $product = Product::query()->create([
            'name' => 'Roasted Arabica Beans',
            'price' => '14.50',
            'stock_quantity' => 9,
        ]);

        $response = $this->actingAs($staff)->post(route('sales.store'), [
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'quantity' => 3,
        ]);

        $response->assertRedirect(route('sales.index'));
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertSame(6, $product->stock_quantity);

        $sale = Sale::query()->latest('id')->firstOrFail();
        $this->assertSame($product->id, $sale->product_id);
        $this->assertSame($customer->id, $sale->customer_id);
        $this->assertSame($staff->id, $sale->sold_by);
        $this->assertSame(3, $sale->quantity);
        $this->assertSame('43.50', $sale->total_price);

        $this->actingAs($staff)
            ->get(route('sales.index'))
            ->assertOk()
            ->assertSee('Roasted Arabica Beans')
            ->assertSee('Teresa Mendoza')
            ->assertSee('Daniel Valdez')
            ->assertSee('$43.50');
    }

    public function test_walk_in_sale_with_null_customer_succeeds(): void
    {
        $staff = User::factory()->create();
        $product = Product::query()->create([
            'name' => 'Cold Brew Bottle',
            'price' => '4.50',
            'stock_quantity' => 10,
        ]);

        $this->actingAs($staff)->post(route('sales.store'), [
            'product_id' => $product->id,
            'customer_id' => '',
            'quantity' => 2,
        ])->assertRedirect(route('sales.index'));

        $product->refresh();
        $this->assertSame(8, $product->stock_quantity);
        $this->assertDatabaseHas('sales', [
            'product_id' => $product->id,
            'customer_id' => null,
            'quantity' => 2,
            'total_price' => '9.00',
        ]);

        $this->actingAs($staff)
            ->get(route('sales.index'))
            ->assertOk()
            ->assertSee('Walk-in Customer');
    }

    public function test_insufficient_stock_rejects_sale_without_modifying_inventory_or_creating_partial_sale(): void
    {
        $staff = User::factory()->create();
        $product = Product::query()->create([
            'name' => 'Raw Wildflower Honey 350g',
            'price' => '11.25',
            'stock_quantity' => 3,
        ]);

        $response = $this->actingAs($staff)
            ->from(route('sales.create'))
            ->post(route('sales.store'), [
                'product_id' => $product->id,
                'customer_id' => '',
                'quantity' => 4,
            ]);

        $response->assertRedirect(route('sales.create'));
        $response->assertSessionHasErrors(['quantity']);
        $response->assertSessionHas('error', 'Insufficient stock for Raw Wildflower Honey 350g. Only 3 units available.');

        $product->refresh();
        $this->assertSame(3, $product->stock_quantity);
        $this->assertSame(0, Sale::query()->count());
    }

    public function test_archived_product_cannot_be_sold(): void
    {
        $staff = User::factory()->create();
        $product = Product::query()->create([
            'name' => 'Discontinued Jam',
            'price' => '8.00',
            'stock_quantity' => 5,
        ]);
        $product->delete();

        $this->actingAs($staff)
            ->from(route('sales.create'))
            ->post(route('sales.store'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ])
            ->assertRedirect(route('sales.create'))
            ->assertSessionHasErrors(['product_id']);

        $this->assertSame(0, Sale::query()->count());
        $this->assertSame(5, Product::withTrashed()->findOrFail($product->id)->stock_quantity);
    }

    public function test_seeded_database_renders_sales_history_and_dashboard_cleanly(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin.reyes')->firstOrFail();

        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertSee('Operations Dashboard');
        $this->actingAs($admin)->get(route('sales.index'))->assertOk()->assertSee('Walk-in Customer');
    }
}
