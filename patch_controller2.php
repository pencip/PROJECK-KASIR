<?php
$content = file_get_contents('app/Http/Controllers/Manager/DashboardController.php');

$newFunction = <<<FUNC
    public function recentTransactions(Request \$request)
    {
        \$query = Transaction::with(['cashier', 'details'])->where('status', 'completed');
        
        if (\$request->filled('search')) {
            \$query->where('invoice_number', 'like', '%' . \$request->search . '%');
        }
        if (\$request->filled('method')) {
            \$query->where('payment_method', \$request->method);
        }
        
        \$recentTransactions = \$query->latest()->take(10)->get();
        
        return view('manager.partials.recent_table_body', compact('recentTransactions'));
    }
FUNC;

// Insert new function
$content = preg_replace('/}\s*$/', "\n$newFunction\n}", $content);

// Update chartData for date range
$oldChartData = <<<PHP
        \$category = \$request->input('category', 'all');
        \$month = \$request->input('month', Carbon::now()->month);
        \$year = \$request->input('year', 2026);

        \$daysInMonth = Carbon::createFromDate(\$year, \$month, 1)->daysInMonth;
        
        \$query = DB::table('transactions')
            ->whereMonth('transactions.created_at', \$month)
            ->whereYear('transactions.created_at', \$year)
            ->where('transactions.status', 'completed');
PHP;

$newChartData = <<<PHP
        \$category = \$request->input('category', 'all');
        \$range = \$request->input('range', 'this_month');
        
        if (\$range === 'today') {
            \$startDate = Carbon::today();
            \$endDate = Carbon::today();
        } elseif (\$range === '7days') {
            \$startDate = Carbon::today()->subDays(6);
            \$endDate = Carbon::today();
        } elseif (\$range === '30days') {
            \$startDate = Carbon::today()->subDays(29);
            \$endDate = Carbon::today();
        } elseif (\$range === 'custom') {
            \$startDate = Carbon::parse(\$request->input('start_date', Carbon::today()->toDateString()));
            \$endDate = Carbon::parse(\$request->input('end_date', Carbon::today()->toDateString()));
        } else {
            // this_month
            \$startDate = Carbon::now()->startOfMonth();
            \$endDate = Carbon::now()->endOfMonth();
        }
        
        \$query = DB::table('transactions')
            ->whereDate('transactions.created_at', '>=', \$startDate)
            ->whereDate('transactions.created_at', '<=', \$endDate)
            ->where('transactions.status', 'completed');
PHP;

$content = str_replace($oldChartData, $newChartData, $content);

// Update the loop for days
$oldLoopStart = <<<PHP
        \$chartLabels = [];
        \$chartData = [];
        \$totalMonthlySales = 0;

        for (\$day = 1; \$day <= \$daysInMonth; \$day++) {
            \$dateObj = Carbon::createFromDate(\$year, \$month, \$day);
PHP;

$newLoopStart = <<<PHP
        \$chartLabels = [];
        \$chartData = [];
        \$totalMonthlySales = 0;
        
        \$currentDate = \$startDate->copy();
        while (\$currentDate->lte(\$endDate)) {
            \$dateObj = \$currentDate;
PHP;
$content = str_replace($oldLoopStart, $newLoopStart, $content);

// Update loop end
$oldLoopEnd = <<<PHP
            if (\$salesData->has(\$dateString)) {
                \$val = (float) \$salesData->get(\$dateString)->total_sales;
                \$chartData[] = \$val;
                \$totalMonthlySales += \$val;
            } else {
                \$chartData[] = 0;
            }
        }
PHP;
$newLoopEnd = <<<PHP
            if (\$salesData->has(\$dateString)) {
                \$val = (float) \$salesData->get(\$dateString)->total_sales;
                \$chartData[] = \$val;
                \$totalMonthlySales += \$val;
            } else {
                \$chartData[] = 0;
            }
            \$currentDate->addDay();
        }
PHP;
$content = str_replace($oldLoopEnd, $newLoopEnd, $content);

// Update total Trx queries to use start/endDate
$oldTrxQuery1 = <<<PHP
                ->whereMonth('transactions.created_at', \$month)
                ->whereYear('transactions.created_at', \$year)
PHP;
$newTrxQuery = <<<PHP
                ->whereDate('transactions.created_at', '>=', \$startDate)
                ->whereDate('transactions.created_at', '<=', \$endDate)
PHP;
$content = str_replace($oldTrxQuery1, $newTrxQuery, $content);

file_put_contents('app/Http/Controllers/Manager/DashboardController.php', $content);
echo "Controller updated.\n";
