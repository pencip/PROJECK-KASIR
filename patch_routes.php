<?php
$content = file_get_contents('routes/web.php');
$content = str_replace(
    "Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart_data');",
    "Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart_data');\n        Route::get('/dashboard/recent-transactions', [DashboardController::class, 'recentTransactions'])->name('dashboard.recent_transactions');",
    $content
);
file_put_contents('routes/web.php', $content);
echo "Routes updated.\n";
