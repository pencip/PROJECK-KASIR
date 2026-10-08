<?php
$content = file_get_contents('resources/views/manager/dashboard.blade.php');

// 1. Update Dropdown HTML
$oldDropdown = <<<HTML
                <select id="filterMonth" style="background:var(--bg-surface); border:1px solid var(--border-card); color:var(--text-secondary); border-radius:8px; padding:6px 10px; font-size:0.75rem; outline:none; cursor:pointer;">
                    @foreach(\$filterMonths as \$m => \$label)
                        <option value="{{ \$m }}">{{ \$label }}</option>
                    @endforeach
                </select>
HTML;

$newDropdown = <<<HTML
                <select id="filterDateRange" style="background:var(--bg-surface); border:1px solid var(--border-card); color:var(--text-secondary); border-radius:8px; padding:6px 10px; font-size:0.75rem; outline:none; cursor:pointer;">
                    <option value="this_month">Bulan Ini</option>
                    <option value="today">Hari Ini</option>
                    <option value="7days">7 Hari Terakhir</option>
                    <option value="30days">30 Hari Terakhir</option>
                    <option value="custom">Custom...</option>
                </select>
                <div id="customDateWrap" style="display:none; align-items:center; gap:8px;">
                    <input type="date" id="startDate" class="filter-input" style="width:auto; padding:4px 8px;" value="{{ date('Y-m-d') }}">
                    <span style="color:var(--text-muted)">-</span>
                    <input type="date" id="endDate" class="filter-input" style="width:auto; padding:4px 8px;" value="{{ date('Y-m-d') }}">
                </div>
HTML;
$content = str_replace($oldDropdown, $newDropdown, $content);

// 2. Update the empty tbody part in blade
$oldTbody = <<<HTML
            <table class="m-table" id="trxTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Invoice</th>
                        <th>Kasir</th>
                        <th>Waktu</th>
                        <th>Metode</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
HTML;
$newTbody = $oldTbody = str_replace('<tbody>', '<tbody id="trxTableBody">', $oldTbody);
$content = str_replace('<tbody>', '<tbody id="trxTableBody">', $content);

// 3. Update JS
$oldJS = <<<JS
// ── Live Client-side Filter ────────────────────────────────────────────────
const rows = document.querySelectorAll('#trxTable tbody tr[data-invoice]');
function doFilter() {
    const q = document.getElementById('srchInv').value.toLowerCase();
    const m = document.getElementById('srchMethod').value.toLowerCase();
    rows.forEach(row => {
        const ok = (!q || row.dataset.invoice.includes(q)) && (!m || row.dataset.method === m);
        row.style.display = ok ? '' : 'none';
    });
}
document.getElementById('srchInv').addEventListener('input', doFilter);
document.getElementById('srchMethod').addEventListener('change', doFilter);
</script>
JS;

$newJS = <<<JS
// ── Server-side Filter ────────────────────────────────────────────────
function doFilter() {
    const q = document.getElementById('srchInv').value;
    const m = document.getElementById('srchMethod').value;
    
    fetch(`/manager/dashboard/recent-transactions?search=\${q}&method=\${m}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('trxTableBody').innerHTML = html;
        });
}

let searchTimeout;
document.getElementById('srchInv').addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(doFilter, 300);
});
document.getElementById('srchMethod').addEventListener('change', doFilter);
</script>
JS;
$content = str_replace($oldJS, $newJS, $content);

// 4. Update Chart JS
$oldChartJS = <<<JS
    // ── Fetch new chart data when filters change ──
    const filterCat = document.getElementById('filterCategory');
    const filterMonth = document.getElementById('filterMonth');

    function updateChart() {
        const cat = filterCat.value;
        const month = filterMonth.value;
        
        fetch(`/manager/dashboard/chart-data?category=\${cat}&month=\${month}`)
JS;

$newChartJS = <<<JS
    // ── Fetch new chart data when filters change ──
    const filterCat = document.getElementById('filterCategory');
    const filterRange = document.getElementById('filterDateRange');
    const customWrap = document.getElementById('customDateWrap');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    function updateChart() {
        const cat = filterCat.value;
        const range = filterRange.value;
        
        if (range === 'custom') {
            customWrap.style.display = 'flex';
        } else {
            customWrap.style.display = 'none';
        }
        
        const sDate = startDate.value;
        const eDate = endDate.value;
        
        fetch(`/manager/dashboard/chart-data?category=\${cat}&range=\${range}&start_date=\${sDate}&end_date=\${eDate}`)
JS;
$content = str_replace($oldChartJS, $newChartJS, $content);

$oldChartJSListeners = <<<JS
    filterCat.addEventListener('change', updateChart);
    filterMonth.addEventListener('change', updateChart);
JS;

$newChartJSListeners = <<<JS
    filterCat.addEventListener('change', updateChart);
    filterRange.addEventListener('change', updateChart);
    startDate.addEventListener('change', updateChart);
    endDate.addEventListener('change', updateChart);
JS;
$content = str_replace($oldChartJSListeners, $newChartJSListeners, $content);

file_put_contents('resources/views/manager/dashboard.blade.php', $content);
echo "Blade JS updated.\n";
