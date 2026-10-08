<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/pos');
        $response->assertRedirect('/login');
    }

    public function test_cashier_can_access_pos(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($cashier)->get('/pos');
        $response->assertStatus(200);
        $response->assertSee('Halaman Kasir POS');
    }

    public function test_manager_and_admin_can_also_access_pos(): void
    {
        $manager = User::factory()->create(['role' => 'manajer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($manager)->get('/pos')->assertStatus(200);
        $this->actingAs($admin)->get('/pos')->assertStatus(200);
    }

    public function test_cashier_cannot_access_admin_panel(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($cashier)->get('/admin/menus');
        $response->assertStatus(403);
    }

    public function test_manager_can_access_dashboard_and_transactions(): void
    {
        $manager = User::factory()->create(['role' => 'manajer']);

        $this->actingAs($manager)->get('/manager/dashboard')->assertStatus(200);
        $this->actingAs($manager)->get('/manager/transactions')->assertStatus(200);
    }

    public function test_cashier_can_submit_transaction_and_stocks_decrement(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $menu = Menu::create([
            'name'      => 'Ayam Bakar',
            'category'  => 'Makanan',
            'price'     => 25000,
            'stock'     => 10,
            'is_active' => true,
        ]);

        $payload = [
            'payment_method' => 'cash',
            'paid_amount'    => 60000,
            'items'          => [
                ['id' => $menu->id, 'qty' => 2]
            ]
        ];

        $response = $this->actingAs($cashier)->postJson('/pos/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.total_amount', 50000);
        $response->assertJsonPath('data.change_amount', 10000);

        // Verify stock decremented: 10 - 2 = 8
        $this->assertEquals(8, $menu->fresh()->stock);

        // Verify transaction database records
        $this->assertDatabaseHas('transactions', [
            'total_amount'   => 50000,
            'paid_amount'    => 60000,
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('transaction_details', [
            'menu_id'  => $menu->id,
            'quantity' => 2,
            'subtotal' => 50000,
        ]);
    }

    public function test_transaction_fails_when_stock_insufficient(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);
        $menu = Menu::create([
            'name'      => 'Sop Buntut',
            'category'  => 'Makanan',
            'price'     => 45000,
            'stock'     => 1,
            'is_active' => true,
        ]);

        $payload = [
            'payment_method' => 'cash',
            'paid_amount'    => 100000,
            'items'          => [
                ['id' => $menu->id, 'qty' => 2] // Requesting 2 when stock is only 1
            ]
        ];

        $response = $this->actingAs($cashier)->postJson('/pos/checkout', $payload);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        // Stock remains 1
        $this->assertEquals(1, $menu->fresh()->stock);
    }
}
