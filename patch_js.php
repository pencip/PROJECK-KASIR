<?php
$content = file_get_contents('resources/views/manager/dashboard.blade.php');

$jsToInsert = <<<JS
    const chart = new ApexCharts(document.querySelector("#salesAreaChart"), options);
    chart.render();

    // ── Fetch new chart data when filters change ──
    const filterCat = document.getElementById('filterCategory');
    const filterMonth = document.getElementById('filterMonth');

    function updateChart() {
        const cat = filterCat.value;
        const month = filterMonth.value;
        
        fetch(`/manager/dashboard/chart-data?category=\${cat}&month=\${month}`)
            .then(res => res.json())
            .then(data => {
                // Update chart
                chart.updateSeries([{
                    name: 'Omzet Penjualan',
                    data: data.data
                }]);
                chart.updateOptions({
                    xaxis: {
                        categories: data.labels
                    }
                });
                
                // Update text below chart
                document.getElementById('chartTotalSales').innerText = data.formatted_sales;
                document.getElementById('chartTotalTrx').innerText = data.total_trx + ' TRX';
            })
            .catch(err => console.error('Error fetching chart data', err));
    }

    filterCat.addEventListener('change', updateChart);
    filterMonth.addEventListener('change', updateChart);
    
    // Call once to load initial data based on default select values
    updateChart();
JS;

$content = str_replace(
    'const chart = new ApexCharts(document.querySelector("#salesAreaChart"), options);
    chart.render();',
    $jsToInsert,
    $content
);

file_put_contents('resources/views/manager/dashboard.blade.php', $content);
echo "JS updated successfully.\n";
