<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Tests\TestCase;

class PosCustomerAndStaffTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_crud_and_null_on_delete_preserves_sales_history(): void
    {
        $staff = User::factory()->create();
        $product = Product::query()->create([
            'name' => 'Country Loaf',
            'price' => '6.00',
            'stock_quantity' => 10,
        ]);

        $this->actingAs($staff)->post(route('customers.store'), [
            'full_name' => 'Elena Cruz',
            'email' => 'elena.cruz@example.com',
            'phone' => '+63 917 111 2222',
        ])->assertRedirect(route('customers.index'));

        $customer = Customer::query()->where('email', 'elena.cruz@example.com')->firstOrFail();

        $this->actingAs($staff)->put(route('customers.update', $customer), [
            'full_name' => 'Elena M. Cruz',
            'email' => 'elena.cruz@example.com',
            'phone' => '',
        ])->assertRedirect(route('customers.index'));

        $customer->refresh();
        $this->assertSame('Elena M. Cruz', $customer->full_name);
        $this->assertNull($customer->phone);

        $sale = Sale::query()->create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'sold_by' => $staff->id,
            'quantity' => 2,
            'total_price' => '12.00',
        ]);

        $this->actingAs($staff)->delete(route('customers.destroy', $customer))->assertRedirect(route('customers.index'));

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
        $this->assertDatabaseHas('sales', ['id' => $sale->id, 'customer_id' => null]);
    }

    public function test_staff_crud_with_256x256_avatar_hashing_and_deletion_guards(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['username' => 'admin.test']);
        $avatar = UploadedFile::fake()->image('staff.jpg', 600, 400);

        $this->actingAs($admin)->post(route('users.store'), [
            'username' => 'cashier.new',
            'full_name' => 'Nina Torres',
            'email' => 'nina.torres@example.com',
            'role' => 'Cashier',
            'password' => 'SecurePass!2026',
            'password_confirmation' => 'SecurePass!2026',
            'avatar' => $avatar,
        ])->assertRedirect(route('users.index'));

        $created = User::query()->where('username', 'cashier.new')->firstOrFail();
        $this->assertTrue(Hash::check('SecurePass!2026', $created->password));
        $this->assertNotNull($created->avatar);
        Storage::disk('public')->assertExists($created->avatar);

        $decoded = Image::decode(Storage::disk('public')->get($created->avatar));
        $this->assertSame(256, $decoded->width());
        $this->assertSame(256, $decoded->height());

        // Edit without changing password preserves existing password
        $this->actingAs($admin)->put(route('users.update', $created), [
            'username' => 'cashier.new',
            'full_name' => 'Nina T. Torres',
            'email' => 'nina.torres@example.com',
            'role' => 'Shift Supervisor',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('users.index'));

        $created->refresh();
        $this->assertSame('Nina T. Torres', $created->full_name);
        $this->assertTrue(Hash::check('SecurePass!2026', $created->password));

        // Self-delete guard
        $this->actingAs($admin)->delete(route('users.destroy', $admin))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        // Staff with sales cannot be deleted
        $product = Product::query()->create(['name' => 'Coffee', 'price' => '4.00', 'stock_quantity' => 5]);
        Sale::query()->create([
            'product_id' => $product->id,
            'customer_id' => null,
            'sold_by' => $created->id,
            'quantity' => 1,
            'total_price' => '4.00',
        ]);

        $this->actingAs($admin)->delete(route('users.destroy', $created))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $created->id]);

        // Unlinked staff user can be deleted and cleans up avatar
        $avatarPath = $created->avatar;
        Sale::query()->where('sold_by', $created->id)->delete();

        $this->actingAs($admin)->delete(route('users.destroy', $created))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $created->id]);
        Storage::disk('public')->assertMissing($avatarPath);
    }
}
