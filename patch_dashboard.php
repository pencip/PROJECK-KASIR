<?php
$content = file_get_contents('app/Http/Controllers/Manager/DashboardController.php');

// Add $filterMonths calculation before return view
$monthsGen = <<<PHP
        // Generate last 6 months for filter dropdown
        \$filterMonths = [];
        for (\$i = 0; \$i < 6; \$i++) {
            \$d = Carbon::now()->subMonths(\$i);
            \$filterMonths[\$d->format('m')] = \$d->translatedFormat('F Y');
        }

        return view(
PHP;

$content = str_replace("        return view(", $monthsGen, $content);
$content = str_replace("'hourlySalesChart'", "'hourlySalesChart',\n            'filterMonths'", $content);

file_put_contents('app/Http/Controllers/Manager/DashboardController.php', $content);
echo "Controller updated with filterMonths.\n";
