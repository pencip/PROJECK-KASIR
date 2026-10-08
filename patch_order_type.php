<?php
$content = file_get_contents('app/Http/Controllers/Manager/DashboardController.php');

$oldOrderType = <<<PHP
        // ── Tipe Pesanan ──────────────────────────────────────────────────────
        // Karena belum ada kolom order_type, kita pakai distribusi dummy yg proporsional
        // berdasarkan jumlah transaksi hari ini (bisa diedit jika kolom sudah tersedia)
        \$orderTypeData = [
            'dine_in'   => (int) round(\$dailyCount * 0.50),
            'takeaway'  => (int) round(\$dailyCount * 0.30),
            'online'    => (int) round(\$dailyCount * 0.20),
        ];
PHP;

$newOrderType = <<<PHP
        // ── Tipe Pesanan ──────────────────────────────────────────────────────
        \$orderTypeStats = Transaction::whereDate('created_at', \$today)
            ->where('status', 'completed')
            ->select('order_type', DB::raw('COUNT(*) as count'))
            ->groupBy('order_type')
            ->get()
            ->keyBy('order_type');

        \$orderTypeData = [
            'dine_in'   => \$orderTypeStats->get('dine_in')?->count ?? 0,
            'takeaway'  => \$orderTypeStats->get('takeaway')?->count ?? 0,
            'online'    => \$orderTypeStats->get('online')?->count ?? 0,
        ];
PHP;

$content = str_replace($oldOrderType, $newOrderType, $content);
file_put_contents('app/Http/Controllers/Manager/DashboardController.php', $content);
echo "Order type updated.\n";
