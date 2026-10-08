@extends('layouts.app')

@section('title', 'Dashboard Analitik - Manajer')
@section('header-title', 'Dashboard Analitik')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════════════════════════
   MANAGER DASHBOARD – Design System
   ══════════════════════════════════════════════════════════════════════════ */

/* ── Page Header ─────────────────────────────────────────────────────────── */
.page-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 22px;
}
.page-welcome h2 {
    font-size: 1.35rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.3px;
    margin: 0;
}
.page-welcome p {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 3px;
}
.refresh-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: var(--text-muted);
    cursor: pointer;
    border: none;
    background: none;
    padding: 6px 12px;
    border: 1px solid var(--border-card);
    border-radius: var(--radius-pill);
    background: var(--bg-card);
    transition: all 0.2s;
    white-space: nowrap;
    flex-shrink: 0;
}
.refresh-btn:hover { color: #ffffff; border-color: rgba(255,255,255,0.2); }

/* ── 4 Summary Cards ─────────────────────────────────────────────────────── */
.highlights-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 18px;
}
@media (max-width: 1100px) { .highlights-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .highlights-grid { grid-template-columns: 1fr; } }

.hl-card {
    background: var(--bg-card);
    border: 1px solid var(--border-card);
    border-radius: 14px;
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
    transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.hl-card:hover {
    border-color: rgba(59,130,246,0.3);
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}
.hl-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: 12px;
}
.hl-badge {
    margin-left: auto;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: var(--radius-pill);
}
.badge-green  { color: #34d399; background: rgba(16,185,129,0.12); }
.badge-blue   { color: #60a5fa; background: rgba(59,130,246,0.12); }
.badge-purple { color: #a78bfa; background: rgba(139,92,246,0.12); }
.hl-value {
    font-size: 1.6rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.5px;
    line-height: 1.1;
}
.hl-currency { font-size: 0.9rem; font-weight: 700; color: var(--text-secondary); }
.hl-sub {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 6px;
}
.hl-sparkline {
    position: absolute;
    bottom: 0; right: 0;
    opacity: 0.18;
    pointer-events: none;
}

/* ── Main 2-Col Section (Chart + 3 Donuts) ───────────────────────────────── */
.main-section {
    display: grid;
    grid-template-columns: 1fr 300px;   /* chart wide, donuts narrow */
    gap: 16px;
    margin-bottom: 24px;
    align-items: stretch;
}
@media (max-width: 1000px) { .main-section { grid-template-columns: 1fr; } }

/* ── Area Chart Card ─────────────────────────────────────────────────────── */
.chart-card {
    background: var(--bg-card);
    border: 1px solid var(--border-card);
    border-radius: 14px;
    padding: 20px 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.card-hdr {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}
.card-title  { font-size: 1.05rem; font-weight: 700; color: #ffffff; letter-spacing: -0.3px; }
.card-sub    { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
.pill-row    { display: flex; gap: 5px; flex-shrink: 0; }
.pill-xs {
    font-size: 0.7rem;
    padding: 4px 12px;
    border-radius: var(--radius-pill);
    border: 1px solid var(--border-card);
    color: var(--text-muted);
    cursor: pointer;
    background: transparent;
    transition: all 0.2s;
}
.pill-xs.active, .pill-xs:hover {
    background: rgba(245,158,11,0.12);
    border-color: rgba(245,158,11,0.35);
    color: #fbbf24;
}
.bar-area { margin-top: 14px; height: 130px; }

/* ── Right Column – 3 Stacked Compact Donuts ─────────────────────────────── */
.donuts-col {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* compact donut card */
.dc-card {
    background: var(--bg-card);
    border: 1px solid var(--border-card);
    border-radius: 14px;
    padding: 16px 20px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.dc-hdr {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.dc-title { font-size: 0.78rem; font-weight: 700; color: #ffffff; }
.dc-nav {
    display: flex;
    gap: 4px;
}
.dc-nav-btn {
    width: 22px; height: 22px;
    border-radius: 6px;
    border: 1px solid var(--border-card);
    background: var(--bg-pill);
    color: var(--text-muted);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all 0.15s;
}
.dc-nav-btn:hover { color: #fff; border-color: rgba(255,255,255,0.2); }

.dc-body {
    display: flex;
    align-items: center;
    gap: 12px;
}
.dc-legend {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.dc-legend-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.7rem;
    color: var(--text-secondary);
}
.dc-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}
.dc-pct {
    margin-left: auto;
    font-weight: 700;
    font-size: 0.72rem;
    color: #ffffff;
}

/* ── Transaction Table Card ──────────────────────────────────────────────── */
.tbl-card {
    background: var(--bg-card);
    border: 1px solid var(--border-card);
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.tbl-hdr {
    padding-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.tbl-title { font-size: 1.05rem; font-weight: 700; color: #ffffff; letter-spacing: -0.3px; }
.tbl-sub   { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
.toolbar {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.tbtn {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    padding: 6px 14px;
    border-radius: 8px;
    border: 1px solid var(--border-card);
    background: var(--bg-surface);
    color: var(--text-secondary);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease-in-out;
    white-space: nowrap;
}
.tbtn:hover { background: rgba(255,255,255,0.05); color: #ffffff; border-color: rgba(255,255,255,0.2); }
.tbtn.accent { color: #ffffff; background: #2563eb; border-color: #2563eb; }
.tbtn.accent:hover { background: #1d4ed8; border-color: #1d4ed8; }

/* filter row */
.filter-row {
    padding: 12px 0;
    margin-bottom: 12px;
    display: none;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}
.filter-input {
    background: var(--bg-surface);
    border: 1px solid var(--border-card);
    color: #fff;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 0.75rem;
    outline: none;
    width: 240px;
    transition: border-color 0.2s;
}
.filter-input:focus { border-color: #3b82f6; }
.filter-select {
    background: var(--bg-surface);
    border: 1px solid var(--border-card);
    color: var(--text-secondary);
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 0.75rem;
    outline: none;
    transition: border-color 0.2s;
}
.filter-select:focus { border-color: #3b82f6; }

/* modern table */
.m-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 4px; }
.m-table thead tr { background: var(--bg-surface); }
.m-table thead th {
    padding: 12px 16px;
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--text-muted);
    text-align: left;
    white-space: nowrap;
}
.m-table thead th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
.m-table thead th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
.m-table tbody tr {
    transition: background 0.2s ease;
}
.m-table tbody tr:hover { background: rgba(255,255,255,0.02); }
.m-table tbody td {
    padding: 14px 16px;
    font-size: 0.8rem;
    color: var(--text-secondary);
    vertical-align: middle;
    border-bottom: 1px solid var(--border-subtle);
}
.m-table tbody tr:last-child td { border-bottom: none; }

/* status/color dots */
.sdot {
    display: inline-block;
    width: 7px; height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}
.sdot-green  { background: #34d399; box-shadow: 0 0 4px #34d39955; }
.sdot-orange { background: #f59e0b; box-shadow: 0 0 4px #f59e0b55; }
.sdot-red    { background: #f87171; box-shadow: 0 0 4px #f8717155; }

/* method badges */
.mbadge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 0.68rem; font-weight: 700;
    padding: 2px 8px; border-radius: var(--radius-pill);
    text-transform: uppercase; letter-spacing: 0.2px;
}
.mb-qris     { background: rgba(139,92,246,0.14);  color: #a78bfa; border: 1px solid rgba(139,92,246,0.22); }
.mb-tunai    { background: rgba(16,185,129,0.11);  color: #34d399; border: 1px solid rgba(16,185,129,0.18); }
.mb-debit    { background: rgba(59,130,246,0.11);  color: #60a5fa; border: 1px solid rgba(59,130,246,0.18); }
.mb-transfer { background: rgba(245,158,11,0.11);  color: #fbbf24; border: 1px solid rgba(245,158,11,0.18); }
.mb-default  { background: rgba(100,116,139,0.12); color: #94a3b8; border: 1px solid rgba(100,116,139,0.18); }

/* status badge */
.sbadge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 0.72rem; font-weight: 600;
    padding: 2px 9px; border-radius: var(--radius-pill);
}
.sb-selesai { color: #34d399; background: rgba(16,185,129,0.08); }
.sb-pending { color: #fbbf24; background: rgba(245,158,11,0.08); }
.sb-batal   { color: #f87171; background: rgba(239,68,68,0.08);  }

/* detail button */
.btn-det {
    font-size: 0.75rem; padding: 6px 14px;
    border-radius: 8px;
    border: 1px solid var(--border-card);
    background: var(--bg-surface);
    color: var(--text-secondary); text-decoration: none;
    transition: all 0.2s ease; white-space: nowrap;
    display: inline-block; font-weight: 500;
}
.btn-det:hover { border-color: rgba(59,130,246,0.4); background: rgba(59,130,246,0.1); color: #60a5fa; }
.inv-text {
    font-family: 'SF Mono','Consolas',monospace;
    font-size: 0.75rem; font-weight: 600; color: #ffffff;
}
.rank-col { color: var(--text-muted); font-weight: 700; font-size: 0.72rem; }
</style>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- Page Header                                                             --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="page-header-row">
    <div class="page-welcome">
        <h2>Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
        <p>Ringkasan operasional kasir — {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <button class="refresh-btn" onclick="location.reload()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
        </svg>
        Refresh Data
    </button>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- Highlights – 4 Cards                                                   --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="highlights-grid">

    {{-- Penjualan Hari Ini --}}
    <div class="hl-card">
        <div class="hl-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Penjualan Hari Ini
            <span class="hl-badge badge-green">+12.8%</span>
        </div>
        <div class="hl-value"><span class="hl-currency">Rp </span>{{ number_format($dailySales, 0, ',', '.') }}</div>
        <div class="hl-sub">{{ $dailyCount }} transaksi selesai hari ini</div>
        <svg class="hl-sparkline" width="80" height="46" viewBox="0 0 80 46">
            <path d="M0,40 Q20,28 35,32 T60,12 T80,4 L80,46 L0,46 Z" fill="#f59e0b"/>
        </svg>
    </div>

    {{-- Total Transaksi --}}
    <div class="hl-card">
        <div class="hl-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            Total Transaksi
            <span class="hl-badge badge-blue">Hari Ini</span>
        </div>
        <div class="hl-value">{{ $dailyCount }}</div>
        <div class="hl-sub">{{ $monthlyCount }} transaksi bulan ini</div>
        <svg class="hl-sparkline" width="80" height="46" viewBox="0 0 80 46">
            <path d="M0,44 Q15,36 25,38 T50,18 T80,8 L80,46 L0,46 Z" fill="#3b82f6"/>
        </svg>
    </div>

    {{-- Rata-rata per Transaksi --}}
    <div class="hl-card">
        <div class="hl-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Rata-rata / TRX
            <span class="hl-badge badge-purple">Per TRX</span>
        </div>
        <div class="hl-value"><span class="hl-currency">Rp </span>{{ number_format($avgTransaction, 0, ',', '.') }}</div>
        <div class="hl-sub">Nilai rata-rata per transaksi hari ini</div>
        <svg class="hl-sparkline" width="80" height="46" viewBox="0 0 80 46">
            <path d="M0,38 Q20,24 35,26 T65,10 T80,6 L80,46 L0,46 Z" fill="#8b5cf6"/>
        </svg>
    </div>

    {{-- Item Terjual --}}
    <div class="hl-card">
        <div class="hl-label">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Item Terjual
            <span class="hl-badge badge-green">Hari Ini</span>
        </div>
        <div class="hl-value">{{ $dailyItemsSold }}</div>
        <div class="hl-sub">Total porsi/item diproses kasir hari ini</div>
        <svg class="hl-sparkline" width="80" height="46" viewBox="0 0 80 46">
            <path d="M0,42 Q10,34 22,36 T48,16 T80,4 L80,46 L0,46 Z" fill="#10b981"/>
        </svg>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- Main Section 1: Area Chart (kiri) + Donut (kanan)                     --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="main-section">

    {{-- ── LEFT: Progress Overview / Area Chart ── --}}
    <div class="chart-card" style="display: flex; flex-direction: column;">
        <!-- Header Card sejajar dropdown -->
        <div class="card-hdr" style="margin-bottom: 20px;">
            <div>
                <h3 class="card-title" style="margin: 0; font-size: 1.125rem;">Progress Penjualan</h3>
                <p class="card-sub" style="margin: 4px 0 0 0;">Tren transaksi & pendapatan 30 hari terakhir</p>
            </div>

            <!-- Dropdown Filter Kanan -->
            <div style="display: flex; align-items: center; gap: 8px;">
                <select id="filterCategory" style="background:var(--bg-surface); border:1px solid var(--border-card); color:var(--text-secondary); border-radius:8px; padding:6px 10px; font-size:0.75rem; outline:none; cursor:pointer;">
                    <option value="all">Semua Kategori</option>
                    <option value="Makanan Utama">Makanan Utama</option>
                    <option value="Minuman">Minuman</option>
                </select>
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
            </div>
        </div>

        <!-- Container Chart -->
        <div id="salesAreaChart" style="flex: 1; min-height: 250px;"></div>
        
        {{-- Bottom stat --}}
        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border-card); padding-top:10px; margin-top:6px;">
            <span style="font-size:0.74rem; color:var(--text-muted);">
                Total Bulan Ini: <strong id="chartTotalSales" style="color:#fff;">Rp {{ number_format($monthlySales,0,',','.') }}</strong>
            </span>
            <span id="chartTotalTrx" style="font-size:0.74rem; color:#34d399; font-weight:700;">{{ $monthlyCount }} TRX</span>
        </div>
    </div>

    {{-- ── RIGHT: Donut Card ── --}}
    @php
        /* shared donut geometry */
        $cx = 64; $cy = 64; $r = 48;
        $circ = 2 * M_PI * $r;
        $gap  = 2;

        /* helper: build segments array */
        if (!function_exists('buildSegs')) {
            function buildSegs(array $data, array $colors, float $circ, float $gap): array {
                $total = array_sum($data) ?: 1;
                $off = 0; $segs = [];
                foreach ($data as $key => $val) {
                    $pct  = $val / $total;
                    $dash = $circ * $pct - $gap;
                    $segs[$key] = ['pct'=>$pct,'dash'=>max($dash,0),'offset'=>$off,'color'=>$colors[$key]];
                    $off += $circ * $pct;
                }
                return $segs;
            }
        }
        
        $pmColors = ['qris'=>'#a78bfa','tunai'=>'#34d399','debit'=>'#60a5fa','transfer'=>'#fbbf24'];
        $pmLabels = ['qris'=>'QRIS','tunai'=>'Tunai / Cash','debit'=>'Debit','transfer'=>'Transfer'];
        $pmTotal  = array_sum($paymentData) ?: 1;
        $pmSegs   = buildSegs($paymentData, $pmColors, $circ, $gap);
    @endphp

    <div class="dc-card">
        <div class="dc-hdr">
            <div class="dc-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2" style="vertical-align:-2px; margin-right:4px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Metode Pembayaran
            </div>
            <div class="dc-nav">
                <div class="dc-nav-btn">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <div class="dc-nav-btn">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>
        </div>
        <div class="dc-body" style="flex-direction:column; align-items:center; padding-top:16px;">
            {{-- Compact Donut SVG --}}
            <svg width="128" height="128" viewBox="0 0 128 128" style="flex-shrink:0;">
                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
                        stroke="rgba(255,255,255,0.06)" stroke-width="10"/>
                @foreach($pmSegs as $key => $seg)
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
                            stroke="{{ $seg['color'] }}" stroke-width="10"
                            stroke-dasharray="{{ $seg['dash'] }} {{ $circ }}"
                            stroke-dashoffset="{{ -$seg['offset'] }}"
                            transform="rotate(-90 {{ $cx }} {{ $cy }})"/>
                @endforeach
                <text x="{{ $cx }}" y="{{ $cy - 2 }}" text-anchor="middle" font-size="20" font-weight="800" fill="#fff">{{ $dailyCount }}</text>
                <text x="{{ $cx }}" y="{{ $cy + 14 }}" text-anchor="middle" font-size="9" fill="#64748b">Total Trx</text>
            </svg>
            {{-- Legend --}}
            <div class="dc-legend" style="display:grid; grid-template-columns:1fr 1fr; gap:12px 16px; width:100%; margin-top:8px;">
                @foreach($paymentData as $key => $val)
                    <div class="dc-legend-row">
                        <span class="dc-dot" style="background:{{ $pmColors[$key] }};"></span>
                        <span>{{ $pmLabels[$key] }}</span>
                        <span class="dc-pct">{{ $pmTotal>0 ? round($val/$pmTotal*100) : 0 }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- Main Section 2: Transaksi Kasir (kiri) + Aksi Cepat (kanan)            --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
<div class="main-section" style="align-items: flex-start;">

    {{-- ── LEFT: Transaksi Kasir Terkini ── --}}
    <div class="tbl-card" style="margin-bottom: 0;">
        <div class="tbl-hdr">
            <div>
                <div class="tbl-title">Transaksi Kasir Terkini</div>
                <div class="tbl-sub">10 transaksi pembayaran terbaru yang telah selesai</div>
            </div>
            <div class="toolbar">
                <button class="tbtn" id="filterToggleBtn">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Cari
                </button>
                <button class="tbtn">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                    Sort
                </button>
                <button class="tbtn">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter
                </button>
            </div>
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
                <tbody id="trxTableBody">
                    @php
                        $dotColors = ['#f59e0b','#60a5fa','#34d399','#a78bfa','#f43f5e','#06b6d4','#fbbf24','#fb7185'];
                    @endphp
                    @forelse($recentTransactions as $idx => $trx)
                        @php $method = strtolower($trx->payment_method); @endphp
                        <tr data-invoice="{{ strtolower($trx->invoice_number) }}" data-method="{{ $method }}">
                            <td class="rank-col">#{{ $idx + 1 }}</td>
                            <td><span class="inv-text">{{ $trx->invoice_number }}</span></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:7px;">
                                    <span class="sdot" style="background:{{ $dotColors[$idx % count($dotColors)] }}; box-shadow:0 0 4px {{ $dotColors[$idx % count($dotColors)] }}55;"></span>
                                    <span style="color:#e2e8f0; font-size:0.78rem;">{{ $trx->cashier ? $trx->cashier->name : 'Kasir' }}</span>
                                </div>
                            </td>
                            <td style="white-space:nowrap;">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                @switch($method)
                                    @case('qris')
                                        <span class="mbadge mb-qris">📱 QRIS</span>
                                        @break
                                    @case('tunai')
                                    @case('cash')
                                        <span class="mbadge mb-tunai">💵 Tunai</span>
                                        @break
                                    @case('debit')
                                        <span class="mbadge mb-debit">💳 Debit</span>
                                        @break
                                    @case('transfer')
                                        <span class="mbadge mb-transfer">🏦 Transfer</span>
                                        @break
                                    @default
                                        <span class="mbadge mb-default">{{ $trx->payment_method }}</span>
                                @endswitch
                            </td>
                            <td>{{ $trx->details->sum('quantity') }} porsi</td>
                            <td style="font-weight:800; color:#34d399; white-space:nowrap;">
                                Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="sbadge sb-selesai">
                                    <span class="sdot sdot-green"></span>
                                    Selesai
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('manager.transactions', ['search' => $trx->invoice_number]) }}" class="btn-det">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; color:var(--text-muted); padding:36px;">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:block;margin:0 auto 8px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Belum ada riwayat transaksi penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── RIGHT: Quick Review / Action Card ── --}}
    <div class="dc-card" style="display:flex; flex-direction:column; justify-content:space-between; align-self: flex-start; height: auto;">
        <div>
            <div class="dc-title" style="font-size:1rem; margin-bottom:2px;">Aksi Cepat</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:16px;">Akses pintasan kasir & resto</div>
            


            {{-- Action Icons --}}
            <div style="display:flex; justify-content:space-between; margin-bottom:24px; padding:0 8px;">
                <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Buka POS">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center; color:#f59e0b;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                    </div>
                </div>
                <a href="{{ route('manager.users.index') }}" style="text-decoration:none; display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Pengguna">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(59,130,246,0.15); display:flex; align-items:center; justify-content:center; color:#60a5fa;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    </div>
                </a>
                <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Promo">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(239,68,68,0.15); display:flex; align-items:center; justify-content:center; color:#f87171;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                </div>
                <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Laporan">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(139,92,246,0.15); display:flex; align-items:center; justify-content:center; color:#a78bfa;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:10px;">
            <button class="tbtn" style="flex:1; justify-content:center;">Kelola</button>
            <button class="tbtn accent" style="flex:1; justify-content:center; background:#111827; border-color:#374151;">Buka Kasir →</button>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
// ── Filter Toggle ──────────────────────────────────────────────────────────
const filterRow = document.getElementById('filterRow');
document.getElementById('filterToggleBtn').addEventListener('click', () => {
    filterRow.style.display = filterRow.style.display === 'flex' ? 'none' : 'flex';
    if (filterRow.style.display === 'flex') document.getElementById('srchInv').focus();
});

// ── Server-side Filter ────────────────────────────────────────────────
function doFilter() {
    const q = document.getElementById('srchInv').value;
    const m = document.getElementById('srchMethod').value;
    
    fetch(`/manager/dashboard/recent-transactions?search=${q}&method=${m}`)
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

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartData30 = @json($monthlySalesChart);
    
    // Gunakan data dari controller
    const chartData = chartData30.data;
    const chartLabels = chartData30.labels;

    const options = {
      series: [{
        name: 'Omzet Penjualan',
        data: chartData
      }],
      chart: {
        type: 'area',
        height: 260,
        toolbar: { show: false },
        zoom: { enabled: false },
        sparkline: { enabled: false }
      },
      colors: ['#f97316'], // Oranye persis referensi
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'dark',
          type: 'vertical',
          shadeIntensity: 0.5,
          gradientToColors: ['#ea580c'],
          inverseColors: false,
          opacityFrom: 0.55,
          opacityTo: 0.05,
          stops: [0, 90, 100]
        }
      },
      stroke: {
        curve: 'smooth',
        width: 3
      },
      markers: {
        size: 5,
        colors: ['#ffffff'],
        strokeColors: '#f97316',
        strokeWidth: 2.5,
        hover: {
          size: 7,
          strokeWidth: 3
        }
      },
      grid: {
        borderColor: 'rgba(255, 255, 255, 0.05)',
        strokeDashArray: 4,
        yaxis: { lines: { show: true } },
        xaxis: { lines: { show: false } }
      },
      xaxis: {
        categories: chartLabels,
        labels: {
          style: {
            colors: '#94a3b8',
            fontSize: '11px',
            fontFamily: 'Inter, sans-serif'
          }
        },
        axisBorder: { show: false },
        axisTicks: { show: false }
      },
      yaxis: {
        labels: {
          style: {
            colors: '#94a3b8',
            fontSize: '11px',
            fontFamily: 'Inter, sans-serif'
          },
          formatter: function (value) {
            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
          }
        }
      },
      tooltip: {
        theme: 'dark',
        custom: function({ series, seriesIndex, dataPointIndex, w }) {
          const val = series[seriesIndex][dataPointIndex];
          const tgl = w.globals.categoryLabels[dataPointIndex];
          return `
            <div class="bg-[#1e1e2d] border border-[#f97316]/30 rounded-xl p-3 shadow-xl text-xs" style="min-width: 140px;">
              <div class="flex items-center justify-between gap-3 mb-1">
                <span class="text-gray-400 font-medium">${tgl} 2026</span>
                <span class="text-emerald-400 font-semibold bg-[#10b981]/10 px-1.5 py-0.5 rounded" style="color: #34d399;">+15%</span>
              </div>
              <div class="text-white font-bold text-sm">Rp ${val.toLocaleString('id-ID')}</div>
              <div class="w-full bg-white/10 rounded-full h-1.5 mt-2" style="background: rgba(255,255,255,0.1);">
                <div class="bg-[#f97316] h-1.5 rounded-full" style="width: 75%; background-color: #f97316;"></div>
              </div>
            </div>
          `;
        }
      }
    };

        const chart = new ApexCharts(document.querySelector("#salesAreaChart"), options);
    chart.render();

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
        
        fetch(`/manager/dashboard/chart-data?category=${cat}&range=${range}&start_date=${sDate}&end_date=${eDate}`)
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
    filterRange.addEventListener('change', updateChart);
    startDate.addEventListener('change', updateChart);
    endDate.addEventListener('change', updateChart);
    
    // Call once to load initial data based on default select values
    updateChart();
});
</script>
@endsection
