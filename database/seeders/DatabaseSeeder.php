<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Menu;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pengguna 3 Role
        $admin = User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@resto.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $manager = User::create([
            'name'     => 'Manajer Resto',
            'email'    => 'manajer@resto.com',
            'password' => Hash::make('password'),
            'role'     => 'manajer',
        ]);

        $cashier = User::create([
            'name'     => 'Kasir Satu',
            'email'    => 'kasir@resto.com',
            'password' => Hash::make('password'),
            'role'     => 'kasir',
        ]);

        // 2. Master Data Menu
        $menus = [
            // Makanan
            ['name' => 'Nasi Goreng Spesial', 'category' => 'Makanan', 'price' => 28000, 'stock' => 50, 'is_active' => true],
            ['name' => 'Ayam Bakar Madu', 'category' => 'Makanan', 'price' => 32000, 'stock' => 30, 'is_active' => true],
            ['name' => 'Mie Goreng Seafood', 'category' => 'Makanan', 'price' => 30000, 'stock' => 25, 'is_active' => true],
            ['name' => 'Sop Buntut Sapi', 'category' => 'Makanan', 'price' => 45000, 'stock' => 15, 'is_active' => true],
            ['name' => 'Sate Ayam Madura', 'category' => 'Makanan', 'price' => 25000, 'stock' => 40, 'is_active' => true],

            // Minuman
            ['name' => 'Es Teh Manis', 'category' => 'Minuman', 'price' => 6000, 'stock' => 100, 'is_active' => true],
            ['name' => 'Kopi Susu Gula Aren', 'category' => 'Minuman', 'price' => 18000, 'stock' => 50, 'is_active' => true],
            ['name' => 'Jus Alpukat Kocok', 'category' => 'Minuman', 'price' => 16000, 'stock' => 30, 'is_active' => true],
            ['name' => 'Lemon Tea Dingin', 'category' => 'Minuman', 'price' => 12000, 'stock' => 45, 'is_active' => true],

            // Snack & Penutup
            ['name' => 'Kentang Goreng Keju', 'category' => 'Cemilan', 'price' => 18000, 'stock' => 35, 'is_active' => true],
            ['name' => 'Pisang Bakar Coklat', 'category' => 'Cemilan', 'price' => 15000, 'stock' => 25, 'is_active' => true],
            ['name' => 'Dimsum Ayam (4 pcs)', 'category' => 'Cemilan', 'price' => 20000, 'stock' => 20, 'is_active' => true],
        ];

        foreach ($menus as $menuData) {
            Menu::create($menuData);
        }

        // 3. Contoh Transaksi Awal untuk Manajer Dashboard
        $sampleMenu1 = Menu::first();
        $sampleMenu2 = Menu::where('category', 'Minuman')->first();

        $trx = Transaction::create([
            'invoice_number' => 'INV-' . date('Ymd') . '-INIT1',
            'user_id'        => $cashier->id,
            'total_amount'   => ($sampleMenu1->price * 2) + $sampleMenu2->price,
            'payment_method' => 'cash',
            'paid_amount'    => 100000,
            'change_amount'  => 100000 - (($sampleMenu1->price * 2) + $sampleMenu2->price),
            'status'         => 'completed',
            'created_at'     => now(),
        ]);

        TransactionDetail::create([
            'transaction_id' => $trx->id,
            'menu_id'        => $sampleMenu1->id,
            'menu_name'      => $sampleMenu1->name,
            'price'          => $sampleMenu1->price,
            'quantity'       => 2,
            'subtotal'       => $sampleMenu1->price * 2,
        ]);

        TransactionDetail::create([
            'transaction_id' => $trx->id,
            'menu_id'        => $sampleMenu2->id,
            'menu_name'      => $sampleMenu2->name,
            'price'          => $sampleMenu2->price,
            'quantity'       => 1,
            'subtotal'       => $sampleMenu2->price,
        ]);
    }
}
