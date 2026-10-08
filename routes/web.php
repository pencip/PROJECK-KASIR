<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\PromotionApprovalController;
use App\Http\Controllers\Manager\UserController;

// ── Auth Routes ──────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ── Root Redirect ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'manajer' => redirect()->route('manager.dashboard'),
            default   => redirect()->route('pos.index'),
        };
    }
    return redirect()->route('login');
});

// ── Protected Routes ──────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // 1. ADMIN: CRUD Menu, dan Promosi (pengajuan + kelola setelah approved)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('menus',      MenuController::class);
        Route::resource('promotions', PromotionController::class);
    });

    // 2. MANAJER & ADMIN: Dashboard analitik, riwayat transaksi
    Route::middleware(['role:manajer,admin'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard',    [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart_data');
        Route::get('/dashboard/recent-transactions', [DashboardController::class, 'recentTransactions'])->name('dashboard.recent_transactions');
        Route::get('/transactions', [DashboardController::class, 'transactions'])->name('transactions');
    });

    // 3. MANAJER only: Kelola Pengguna dan Verifikasi/approval promosi dari Admin
    Route::middleware(['role:manajer'])->prefix('manager')->name('manager.')->group(function () {
        Route::resource('users',               UserController::class);
        Route::get('/promotions',              [PromotionApprovalController::class, 'index'])->name('promotions.index');
        Route::post('/promotions/{id}/verify', [PromotionApprovalController::class, 'verify'])->name('promotions.verify');
    });

    // 4. KASIR / ADMIN / MANAJER: Halaman POS Kasir
    Route::middleware(['role:kasir,admin,manajer'])->prefix('pos')->name('pos.')->group(function () {
        Route::get('/',         [PosController::class, 'index'])->name('index');
        Route::post('/checkout',[PosController::class, 'store'])->name('checkout');
    });
});