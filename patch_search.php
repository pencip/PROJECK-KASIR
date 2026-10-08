<?php
$content = file_get_contents('resources/views/manager/dashboard.blade.php');

// 1. Remove Dummy Search Bar from Aksi Cepat
$dummySearch = <<<HTML
            {{-- Dummy Search Bar --}}
            <div style="background:var(--bg-surface); border:1px solid var(--border-card); border-radius:8px; padding:8px 12px; display:flex; align-items:center; gap:8px; margin-bottom:20px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <span style="font-size:0.75rem; color:var(--text-muted);">Pilih aksi cepat...</span>
            </div>
HTML;
$content = str_replace($dummySearch, '', $content);

// 2. Add filterRow to Transaksi Kasir Terkini and enable button
$content = str_replace('<button class="tbtn" id="filterToggleBtn" style="pointer-events:none;">', '<button class="tbtn" id="filterToggleBtn">', $content);

$filterRowHTML = <<<HTML
        </div>

        <div id="filterRow" class="filter-row">
            <input type="text" id="srchInv" class="filter-input" placeholder="Cari no. invoice...">
            <select id="srchMethod" class="filter-select">
                <option value="">Semua Metode</option>
                <option value="qris">QRIS</option>
                <option value="tunai">Tunai / Cash</option>
                <option value="debit">Debit</option>
                <option value="transfer">Transfer</option>
            </select>
        </div>

        <div style="overflow-x:auto;">
HTML;
$content = str_replace("        </div>\n\n        <div style=\"overflow-x:auto;\">", $filterRowHTML, $content);

// 3. Uncomment JS
$content = str_replace('/*
// ── Filter Toggle', '// ── Filter Toggle', $content);
$content = str_replace('document.getElementById(\'srchMethod\').addEventListener(\'change\', doFilter);
*/', 'document.getElementById(\'srchMethod\').addEventListener(\'change\', doFilter);', $content);

file_put_contents('resources/views/manager/dashboard.blade.php', $content);
echo "Done.\n";
