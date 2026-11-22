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
            ->assertSee('Walk-in');
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
            ->followingRedirects()
            ->from(route('sales.create'))
            ->post(route('sales.store'), [
                'product_id' => $product->id,
                'customer_id' => '',
                'quantity' => 4,
            ]);

        $response->assertOk();
        $response->assertSee('Insufficient stock for Raw Wildflower Honey 350g. Only 3 units available.');
        $response->assertSee('Set to 3');
        $this->assertMatchesRegularExpression('/<button[^>]*id="complete-sale-btn"[^>]*disabled/s', $response->getContent());

        $product->refresh();
        $this->assertSame(3, $product->stock_quantity);
        $this->assertSame(0, Sale::query()->count());
    }

    public function test_insufficient_stock_flashes_available_stock_without_duplicate_banner(): void
    {
        $staff = User::factory()->create();
        $product = Product::query()->create([
            'name' => 'Cold Pressed Olive Oil',
            'price' => '18.00',
            'stock_quantity' => 2,
        ]);

        $response = $this->actingAs($staff)
            ->from(route('sales.create'))
            ->post(route('sales.store'), [
                'product_id' => $product->id,
                'customer_id' => '',
                'quantity' => 5,
            ]);

        $response->assertRedirect(route('sales.create'));
        $response->assertSessionHasErrors(['quantity']);
        $response->assertSessionMissing('error');
        $response->assertSessionHas('available_stock', 2);
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
        $this->actingAs($admin)->get(route('sales.index'))->assertOk()->assertSee('Walk-in');
    }

    public function test_sales_receipt_page_and_ledger_filters_work_correctly(): void
    {
        $cashierA = User::factory()->create(['full_name' => 'Cashier Alpha', 'username' => 'cashier.alpha']);
        $cashierB = User::factory()->create(['full_name' => 'Cashier Beta', 'username' => 'cashier.beta']);

        $beans = Product::query()->create(['name' => 'Filter Beans', 'price' => '12.00', 'stock_quantity' => 10]);
        $matcha = Product::query()->create(['name' => 'Ceremonial Matcha', 'price' => '18.50', 'stock_quantity' => 10]);

        $saleA = Sale::query()->create([
            'product_id' => $beans->id,
            'customer_id' => null,
            'sold_by' => $cashierA->id,
            'quantity' => 2,
            'total_price' => '24.00',
        ]);
        $saleB = Sale::query()->create([
            'product_id' => $matcha->id,
            'customer_id' => null,
            'sold_by' => $cashierB->id,
            'quantity' => 1,
            'total_price' => '18.50',
        ]);

        $this->actingAs($cashierA)
            ->get(route('sales.show', $saleA))
            ->assertOk()
            ->assertSee('Receipt')
            ->assertSee('#'.$saleA->id)
            ->assertSee('Filter Beans')
            ->assertSee('$24.00')
            ->assertSee('Cashier Alpha');

        $this->actingAs($cashierA)
            ->get(route('sales.index', ['q' => 'Matcha']))
            ->assertOk()
            ->assertSee('Ceremonial Matcha')
            ->assertDontSee('Filter Beans');

        $this->actingAs($cashierA)
            ->get(route('sales.index', ['staff' => $cashierA->id]))
            ->assertOk()
            ->assertSee('Filter Beans')
            ->assertDontSee('Ceremonial Matcha');

        $this->actingAs($cashierA)
            ->get(route('sales.index', ['q' => '#'.$saleB->id]))
            ->assertOk()
            ->assertSee('Ceremonial Matcha')
            ->assertDontSee('Filter Beans');

        $this->actingAs($cashierA)
            ->get(route('sales.index', ['from' => 'today', 'to' => 'invalid-date']))
            ->assertOk()
            ->assertSee('Filter Beans')
            ->assertSee('Ceremonial Matcha');

        $this->actingAs($cashierA)
            ->get(route('sales.index', ['from' => now()->addDay()->toDateString()]))
            ->assertOk()
            ->assertSee('No Matching Sales Found');
    }
}
