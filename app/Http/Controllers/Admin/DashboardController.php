<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Menu;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Statistik Pengguna ───────────────────────────────────────────────
        $totalUsers = User::count();
        $roleDistribution = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->keyBy('role');

        $kasirCount = $roleDistribution->get('kasir')?->count ?? 0;
        $managerCount = $roleDistribution->get('manajer')?->count ?? 0;
        $adminCount = $roleDistribution->get('admin')?->count ?? 0;

        // ── Statistik Menu ───────────────────────────────────────────────────
        $totalMenus = Menu::count();
        $activeMenus = Menu::where('is_active', true)->count();
        $outOfStockCount = Menu::where('stock', '<=', 0)->count();
        $lowStockCount = Menu::where('stock', '>', 0)->where('stock', '<=', 5)->count();

        // ── Statistik Promo ──────────────────────────────────────────────────
        $totalPromos = Promotion::count();
        $pendingPromos = 0;
        if (Schema::hasColumn('promotions', 'status')) {
            $pendingPromos = Promotion::where('status', 'pending')->count();
        }

        // ── Tabel Peringatan: Stok Menipis ──────────────────────────────────
        $lowStockMenus = Menu::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get();

        // ── Karyawan Terkini ─────────────────────────────────────────────────
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'kasirCount', 'managerCount', 'adminCount',
            'totalMenus', 'activeMenus', 'outOfStockCount', 'lowStockCount',
            'totalPromos', 'pendingPromos',
            'lowStockMenus', 'recentUsers'
        ));
    }
}
