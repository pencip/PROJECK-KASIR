<?php
$content = file_get_contents('app/Http/Controllers/Manager/DashboardController.php');

$newFunction = <<<FUNC
    public function chartData(Request \$request)
    {
        \$category = \$request->input('category', 'all');
        \$month = \$request->input('month', Carbon::now()->month);
        \$year = \$request->input('year', 2026);

        \$daysInMonth = Carbon::createFromDate(\$year, \$month, 1)->daysInMonth;
        
        \$query = DB::table('transactions')
            ->whereMonth('transactions.created_at', \$month)
            ->whereYear('transactions.created_at', \$year)
            ->where('transactions.status', 'completed');

        if (\$category !== 'all') {
            \$query->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                  ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
                  ->where('menus.category', \$category);
                  
            \$salesData = \$query->select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(transaction_details.subtotal) as total_sales'),
                DB::raw('COUNT(DISTINCT transactions.id) as total_trx')
            )->groupBy('date')->get()->keyBy('date');
        } else {
            \$salesData = \$query->select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(transactions.total_amount) as total_sales'),
                DB::raw('COUNT(transactions.id) as total_trx')
            )->groupBy('date')->get()->keyBy('date');
        }

        \$chartLabels = [];
        \$chartData = [];
        \$totalMonthlySales = 0;

        for (\$day = 1; \$day <= \$daysInMonth; \$day++) {
            \$dateObj = Carbon::createFromDate(\$year, \$month, \$day);
            \$dateString = \$dateObj->format('Y-m-d');
            \$chartLabels[] = \$dateObj->format('d M');
            
            if (\$salesData->has(\$dateString)) {
                \$val = (float) \$salesData->get(\$dateString)->total_sales;
                \$chartData[] = \$val;
                \$totalMonthlySales += \$val;
            } else {
                \$chartData[] = 0;
            }
        }
        
        if (\$category !== 'all') {
            \$totalMonthlyTrx = DB::table('transactions')
                ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->join('menus', 'transaction_details.menu_id', '=', 'menus.id')
                ->whereMonth('transactions.created_at', \$month)
                ->whereYear('transactions.created_at', \$year)
                ->where('transactions.status', 'completed')
                ->where('menus.category', \$category)
                ->distinct('transactions.id')
                ->count('transactions.id');
        } else {
            \$totalMonthlyTrx = DB::table('transactions')
                ->whereMonth('transactions.created_at', \$month)
                ->whereYear('transactions.created_at', \$year)
                ->where('transactions.status', 'completed')
                ->count('id');
        }

        return response()->json([
            'labels' => \$chartLabels,
            'data' => \$chartData,
            'total_sales' => \$totalMonthlySales,
            'total_trx' => \$totalMonthlyTrx,
            'formatted_sales' => 'Rp ' . number_format(\$totalMonthlySales, 0, ',', '.')
        ]);
    }
}
FUNC;

$content = preg_replace('/}\s*$/', "\n$newFunction\n", $content);
file_put_contents('app/Http/Controllers/Manager/DashboardController.php', $content);
echo "Controller updated successfully.\n";
