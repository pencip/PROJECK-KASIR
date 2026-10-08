<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // ── Statistik Utama ──────────────────────────────────────────────────
        $dailySales = Transaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $monthlySales = Transaction::whereMonth('created_at', $thisMonth)
            ->whereYear('created_at', $thisYear)
            ->where('status', 'completed')
            ->sum('total_amount');

        $dailyCount = Transaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        $monthlyCount = Transaction::whereMonth('created_at', $thisMonth)
            ->whereYear('created_at', $thisYear)
            ->where('status', 'completed')
            ->count();

        // Rata-rata per transaksi hari ini
        $avgTransaction = $dailyCount > 0 ? round($dailySales / $dailyCount) : 0;

        // Total item terjual hari ini
        $dailyItemsSold = Transaction::whereDate('transactions.created_at', $today)
            ->where('transactions.status', 'completed')
            ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->sum('transaction_details.quantity');

        // ── Distribusi Metode Pembayaran ─────────────────────────────────────
        $paymentStats = Transaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy(fn($r) => strtolower($r->payment_method));

        $totalTrxToday = $dailyCount ?: 1; // avoid division by zero
        $paymentData = [
            'qris'     => $paymentStats->get('qris')?->count ?? 0,
            'tunai'    => $paymentStats->get('tunai')?->count ?? $paymentStats->get('cash')?->count ?? 0,
            'debit'    => $paymentStats->get('debit')?->count ?? 0,
            'transfer' => $paymentStats->get('transfer')?->count ?? 0,
        ];

        // ── Kategori Menu Terlaris ────────────────────────────────────────────
        $categoryStats = DB::table('transaction_details')
            ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'completed')
            ->whereDate('transactions.created_at', $today)
            ->select('menus.category', DB::raw('SUM(transaction_details.quantity) as total_qty'))
            ->groupBy('menus.category')
            ->orderByDesc('total_qty')
            ->get()
            ->keyBy('category');

        $totalPortions = $categoryStats->sum('total_qty') ?: 1;
        $categoryData = [
            'makanan'  => $categoryStats->get('makanan')?->total_qty ?? $categoryStats->get('Makanan Utama')?->total_qty ?? 0,
            'minuman'  => $categoryStats->get('minuman')?->total_qty ?? $categoryStats->get('Minuman')?->total_qty ?? 0,
            'snack'    => $categoryStats->get('snack')?->total_qty ?? $categoryStats->get('Snack')?->total_qty ?? 0,
            'paket'    => $categoryStats->get('paket')?->total_qty ?? $categoryStats->get('Paket Promo')?->total_qty ?? 0,
        ];

        // ── Tipe Pesanan ──────────────────────────────────────────────────────
        $orderTypeStats = Transaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->select('order_type', DB::raw('COUNT(*) as count'))
            ->groupBy('order_type')
            ->get()
            ->keyBy('order_type');

        $orderTypeData = [
            'dine_in'   => $orderTypeStats->get('dine_in')?->count ?? 0,
            'takeaway'  => $orderTypeStats->get('takeaway')?->count ?? 0,
            'online'    => $orderTypeStats->get('online')?->count ?? 0,
        ];

        // ── Transaksi Terkini ─────────────────────────────────────────────────
        $recentTransactions = Transaction::with(['cashier', 'details'])
            ->latest()
            ->take(10)
            ->get();

        // ── Data Chart Penjualan (Bulanan & Harian) ──────────────────────────
        // Tren 30 Hari
        $monthlySalesChart = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $monthlySalesChart['labels'][] = $date->format('d M');
            $monthlySalesChart['data'][] = (float) Transaction::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount');
        }

        // Tren Jam Ramai (Hari ini)
        $hourlySalesChart = [];
        for ($i = 10; $i <= 22; $i++) {
            $hourlySalesChart['labels'][] = sprintf('%02d:00', $i);
            $hourlySalesChart['data'][] = (float) Transaction::whereDate('created_at', $today)
                ->whereTime('created_at', '>=', sprintf('%02d:00:00', $i))
                ->whereTime('created_at', '<', sprintf('%02d:00:00', $i + 1))
                ->where('status', 'completed')
                ->count(); // count transactions per hour
        }

        // Generate last 6 months for filter dropdown
        $filterMonths = [];
        for ($i = 0; $i < 6; $i++) {
            $d = Carbon::now()->subMonths($i);
            $filterMonths[$d->format('m')] = $d->translatedFormat('F Y');
        }

        return view('manager.dashboard', compact(
            'dailySales',
            'monthlySales',
            'dailyCount',
            'monthlyCount',
            'avgTransaction',
            'dailyItemsSold',
            'paymentData',
            'totalTrxToday',
            'categoryData',
            'totalPortions',
            'orderTypeData',
            'recentTransactions',
            'monthlySalesChart',
            'hourlySalesChart',
            'filterMonths'
        ));
    }

    public function transactions(Request $request)
    {
        $query = Transaction::with(['cashier', 'details']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->latest()->paginate(15);

        // Generate last 6 months for filter dropdown
        $filterMonths = [];
        for ($i = 0; $i < 6; $i++) {
            $d = Carbon::now()->subMonths($i);
            $filterMonths[$d->format('m')] = $d->translatedFormat('F Y');
        }

        return view('manager.transactions', compact('transactions'));
    }

    public function chartData(Request $request)
    {
        $category = $request->input('category', 'all');
        $range = $request->input('range', 'this_month');
        
        if ($range === 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today();
        } elseif ($range === '7days') {
            $startDate = Carbon::today()->subDays(6);
            $endDate = Carbon::today();
        } elseif ($range === '30days') {
            $startDate = Carbon::today()->subDays(29);
            $endDate = Carbon::today();
        } elseif ($range === 'custom') {
            $startDate = Carbon::parse($request->input('start_date', Carbon::today()->toDateString()));
            $endDate = Carbon::parse($request->input('end_date', Carbon::today()->toDateString()));
        } else {
            // this_month
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        
        $query = DB::table('transactions')
            ->whereDate('transactions.created_at', '>=', $startDate)
            ->whereDate('transactions.created_at', '<=', $endDate)
            ->where('transactions.status', 'completed');

        if ($category !== 'all') {
            $query->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                  ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
                  ->where('menus.category', $category);
                  
            $salesData = $query->select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(transaction_details.subtotal) as total_sales'),
                DB::raw('COUNT(DISTINCT transactions.id) as total_trx')
            )->groupBy('date')->get()->keyBy('date');
        } else {
            $salesData = $query->select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(transactions.total_amount) as total_sales'),
                DB::raw('COUNT(transactions.id) as total_trx')
            )->groupBy('date')->get()->keyBy('date');
        }

        $chartLabels = [];
        $chartData = [];
        $totalMonthlySales = 0;
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateObj = $currentDate;
            $dateString = $dateObj->format('Y-m-d');
            $chartLabels[] = $dateObj->format('d M');
            
            if ($salesData->has($dateString)) {
                $val = (float) $salesData->get($dateString)->total_sales;
                $chartData[] = $val;
                $totalMonthlySales += $val;
            } else {
                $chartData[] = 0;
            }
            $currentDate->addDay();
        }
        
        if ($category !== 'all') {
            $totalMonthlyTrx = DB::table('transactions')
                ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
                ->whereDate('transactions.created_at', '>=', $startDate)
                ->whereDate('transactions.created_at', '<=', $endDate)
                ->where('transactions.status', 'completed')
                ->where('menus.category', $category)
                ->distinct('transactions.id')
                ->count('transactions.id');
        } else {
            $totalMonthlyTrx = DB::table('transactions')
                ->whereDate('transactions.created_at', '>=', $startDate)
                ->whereDate('transactions.created_at', '<=', $endDate)
                ->where('transactions.status', 'completed')
                ->count('id');
        }

        return response()->json([
            'labels' => $chartLabels,
            'data' => $chartData,
            'total_sales' => $totalMonthlySales,
            'total_trx' => $totalMonthlyTrx,
            'formatted_sales' => 'Rp ' . number_format($totalMonthlySales, 0, ',', '.')
        ]);
    }

    public function recentTransactions(Request $request)
    {
        $query = Transaction::with(['cashier', 'details'])->where('status', 'completed');
        
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }
        
        $recentTransactions = $query->latest()->take(10)->get();
        
        return view('manager.partials.recent_table_body', compact('recentTransactions'));
    }
}