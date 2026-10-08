<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Operasional Kasir - Resto</title>
    <link rel="stylesheet" href="{{ asset('css/dark-theme.css') }}">
    <style>
        /* POS Specific Dark Styles */
        .pos-layout {
            display: flex;
            flex: 1;
            height: calc(100vh - 70px);
            overflow: hidden;
        }

        /* Area Menu (Tengah) */
        .pos-menu-area {
            flex: 7;
            display: flex;
            flex-direction: column;
            padding: 24px;
            overflow-y: auto;
        }

        .category-filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 22px;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .pos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 16px;
        }

        .pos-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: var(--radius-lg);
            padding: 18px;
            cursor: pointer;
            transition: transform 0.15s, border-color 0.15s, box-shadow 0.15s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            user-select: none;
        }

        .pos-card:hover {
            transform: translateY(-3px);
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.6), var(--shadow-glow);
        }

        .pos-card.out-of-stock {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
            filter: grayscale(0.8);
        }

        .pos-card-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 12px;
        }

        .pos-card-category {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .pos-card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .pos-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            border-top: 1px solid var(--border-subtle);
            padding-top: 10px;
        }

        .pos-card-price {
            font-size: 1.15rem;
            font-weight: 800;
            color: #60a5fa;
        }

        .pos-card-stock {
            font-size: 0.75rem;
            color: var(--text-secondary);
            background-color: var(--bg-pill);
            padding: 3px 8px;
            border-radius: var(--radius-pill);
            border: 1px solid var(--border-subtle);
        }

        .badge-habis {
            position: absolute;
            top: 14px;
            right: 14px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: var(--radius-pill);
        }

        /* Gambar menu di kartu POS */
        .pos-card-img-wrap {
            width: 100%;
            height: 110px;
            border-radius: var(--radius-md);
            overflow: hidden;
            margin-bottom: 12px;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
        }

        .pos-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.2s ease;
        }

        .pos-card:hover .pos-card-img {
            transform: scale(1.05);
        }

        /* Sidebar Keranjang (Kanan) */
        .pos-cart-sidebar {
            flex: 3;
            min-width: 380px;
            max-width: 440px;
            background-color: var(--bg-surface);
            border-left: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
        }

        .cart-header-box {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-items-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 16px 24px;
        }

        .cart-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
            text-align: center;
        }

        .cart-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            margin-bottom: 10px;
        }

        .cart-row-title {
            font-weight: 700;
            font-size: 0.92rem;
            color: var(--text-primary);
            margin-bottom: 3px;
        }

        .cart-row-price {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .cart-qty-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: var(--bg-pill);
            padding: 3px 6px;
            border-radius: var(--radius-pill);
            border: 1px solid var(--border-subtle);
        }

        .btn-qty-mini {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: var(--bg-card);
            border: 1px solid var(--border-subtle);
            color: var(--text-primary);
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-qty-mini:hover {
            background-color: var(--accent-blue);
            color: #ffffff;
        }

        .qty-val-label {
            font-weight: 700;
            font-size: 0.85rem;
            min-width: 18px;
            text-align: center;
            color: var(--text-primary);
        }

        /* Checkout Panel */
        .cart-checkout-box {
            padding: 20px 24px;
            background-color: var(--bg-card);
            border-top: 1px solid var(--border-subtle);
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            font-size: 0.88rem;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .summary-line.grand-total {
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            border-top: 1px dashed var(--border-subtle);
            padding-top: 12px;
            margin-top: 10px;
            margin-bottom: 16px;
        }

        .grand-total-val {
            color: #60a5fa;
            text-shadow: 0 0 12px rgba(96, 165, 250, 0.4);
        }

        .pay-toggle-box {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
        }

        .pay-toggle-btn {
            flex: 1;
            padding: 10px;
            border-radius: var(--radius-pill);
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .pay-toggle-btn.active {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #ffffff;
            border-color: #3b82f6;
            box-shadow: var(--shadow-glow);
        }

        .cash-box-inner {
            margin-bottom: 14px;
        }

        .cash-input-field {
            width: 100%;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-card);
            border-radius: var(--radius-md);
            color: #ffffff;
            padding: 10px 14px;
            font-size: 1rem;
            font-weight: 700;
            outline: none;
            margin-bottom: 8px;
        }

        .cash-input-field:focus {
            border-color: var(--accent-blue);
        }

        .quick-cash-row {
            display: flex;
            gap: 6px;
            margin-bottom: 10px;
        }

        .quick-pill {
            flex: 1;
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            padding: 5px 0;
            border-radius: var(--radius-pill);
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s;
        }

        .quick-pill:hover {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
            background-color: var(--bg-pill);
        }

        .change-result-box {
            background-color: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: var(--radius-md);
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            font-weight: 700;
            color: #34d399;
        }

        .btn-order-checkout {
            width: 100%;
            padding: 14px;
            background-color: #ffffff;
            color: #090a0f;
            border: none;
            border-radius: var(--radius-pill);
            font-size: 0.95rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.15);
        }

        .btn-order-checkout:hover {
            background-color: #f1f5f9;
            transform: translateY(-1px);
        }

        .btn-order-checkout:disabled {
            background-color: var(--bg-pill);
            color: var(--text-muted);
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
            border: 1px solid var(--border-subtle);
        }

        /* Modal QRIS */
        .modal-glass-backdrop {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-glass-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: var(--radius-lg);
            padding: 28px;
            max-width: 360px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.8);
        }

        .qr-render-frame {
            background-color: #ffffff;
            border-radius: var(--radius-md);
            padding: 16px;
            width: 210px;
            height: 210px;
            margin: 18px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== Panel Promosi Aktif di POS Kasir ===== */
        .promo-panel-section { margin-bottom: 22px; }
        .promo-panel-header { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; }
        .promo-panel-title { font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); }
        .promo-badge-live { font-size: 0.65rem; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669); color: #fff; padding: 2px 8px; border-radius: 20px; animation: pulse-live 2s infinite; }
        @keyframes pulse-live { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        .promo-scroll-track { display: flex; gap: 14px; overflow-x: auto; padding-bottom: 6px; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.1) transparent; }
        .promo-card { flex: 0 0 auto; width: 200px; background: linear-gradient(145deg, rgba(16,185,129,0.08), rgba(5,150,105,0.05)); border: 1px solid rgba(16,185,129,0.25); border-radius: var(--radius-lg); padding: 14px; transition: border-color 0.2s, transform 0.2s; }
        .promo-card:hover { border-color: rgba(16,185,129,0.5); transform: translateY(-2px); }
        .promo-card-img { width: 100%; height: 90px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; border: 1px solid rgba(255,255,255,0.08); }
        .promo-card-img-placeholder { width: 100%; height: 90px; border-radius: 8px; margin-bottom: 10px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-size: 2rem; }
        .promo-card-name { font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin-bottom: 5px; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .promo-card-discount { font-size: 1rem; font-weight: 800; color: #34d399; margin-bottom: 4px; }
        .promo-card-periode { font-size: 0.68rem; color: var(--text-muted); margin-bottom: 4px; }
        .promo-card-status-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 0.67rem; font-weight: 700; background: rgba(16,185,129,0.15); color: #34d399; padding: 2px 7px; border-radius: 20px; border: 1px solid rgba(16,185,129,0.2); }
        .promo-card-note { font-size: 0.68rem; color: var(--text-muted); margin-top: 5px; font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .promo-verified-label { font-size: 0.65rem; color: #60a5fa; margin-top: 4px; }

        /* Thermal Receipt (@media print) */
        #pos-thermal-receipt {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden !important;
            }
            #pos-thermal-receipt, #pos-thermal-receipt * {
                visibility: visible !important;
            }
            #pos-thermal-receipt {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 58mm;
                padding: 4px;
                font-family: 'Courier New', Courier, monospace;
                font-size: 11px;
                line-height: 1.25;
                color: #000000 !important;
                background: #ffffff !important;
            }
            .pr-center { text-align: center; }
            .pr-divider { border-bottom: 1px dashed #000000; margin: 5px 0; }
            .pr-flex { display: flex; justify-content: space-between; margin-bottom: 2px; }
            .pr-bold { font-weight: bold; }
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <!-- Left Navigation Rail -->
    <aside class="sidebar-rail">
        <a href="/" class="brand-icon" title="Resto POS">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 3v12a6 6 0 0 0 12 0V3"></path>
            </svg>
        </a>

        <nav class="nav-rail-menu">
            @if(in_array(auth()->user()->role, ['admin', 'manajer']))
                <a href="{{ route('manager.dashboard') }}" class="nav-rail-item" title="Dashboard Analitik">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </a>
            @endif

            <a href="{{ route('pos.index') }}" class="nav-rail-item active" title="Halaman POS Kasir">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            </a>

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.menus.index') }}" class="nav-rail-item" title="Master Data Menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'manajer']))
                <a href="{{ route('manager.transactions') }}" class="nav-rail-item" title="Riwayat Transaksi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </a>
            @endif

            @if(auth()->user()->role === 'manajer')
                <a href="{{ route('manager.users.index') }}" class="nav-rail-item" title="Manajemen Pengguna">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </a>
            @endif
        </nav>

        <div class="nav-rail-bottom">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-rail-item" style="border: none; background: transparent; cursor: pointer;" title="Logout">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Application Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navigation Bar -->
        <header class="top-header">
            <div class="header-left">
                <div class="dropdown-badge">
                    <span>Halaman Kasir POS</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
            </div>

            <!-- Real-time Menu Search -->
            <div class="header-search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="menu-search-input" placeholder="Cari nama menu secara instan..." onkeyup="filterMenuSearch()">
            </div>

            <div class="header-right">
                <div class="header-pill-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span>2 baru</span>
                </div>

                <div class="header-pill-btn">
                    <span>&lsaquo; Hari Ini, {{ date('d M Y') }} &rsaquo;</span>
                </div>

                <div class="header-user-profile">
                    <div class="avatar-circle">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-text-info">
                        <span class="user-name-title">{{ auth()->user()->name }}</span>
                        <span class="user-role-subtitle">&#64;{{ auth()->user()->role }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- POS Workspace Layout -->
        <div class="pos-layout">
            <!-- Left: Catalog & Categories -->
            <div class="pos-menu-area">
                <div class="category-filter-bar">
                    <button class="pill-filter-btn active" onclick="selectCategory('All', this)">Semua Menu</button>
                    @foreach($categories as $category)
                        <button class="pill-filter-btn" onclick="selectCategory('{{ $category }}', this)">{{ $category }}</button>
                    @endforeach
                </div>

                {{-- ===== PANEL PROMOSI AKTIF ===== --}}
                @if($activePromotions->count() > 0)
                <div class="promo-panel-section">
                    <div class="promo-panel-header">
                        <span class="promo-panel-title">🎉 Promosi Aktif Hari Ini</span>
                        <span class="promo-badge-live">● LIVE</span>
                        <span style="font-size:0.72rem;color:var(--text-muted);">({{ $activePromotions->count() }} promo berlaku)</span>
                    </div>
                    <div class="promo-scroll-track">
                        @foreach($activePromotions as $promo)
                        <div class="promo-card">
                            {{-- Gambar Produk --}}
                            @if($promo->gambar)
                                <img src="{{ asset('storage/' . $promo->gambar) }}"
                                     alt="{{ $promo->nama_promo }}"
                                     class="promo-card-img">
                            @else
                                <div class="promo-card-img-placeholder">🎁</div>
                            @endif

                            {{-- Nama Promo --}}
                            <div class="promo-card-name" title="{{ $promo->nama_promo }}">{{ $promo->nama_promo }}</div>

                            {{-- Diskon --}}
                            <div class="promo-card-discount">
                                @if($promo->tipe_diskon === 'persen')
                                    ✂️ Diskon {{ $promo->nilai_diskon }}%
                                @else
                                    ✂️ Hemat Rp {{ number_format($promo->nilai_diskon, 0, ',', '.') }}
                                @endif
                            </div>

                            {{-- Periode --}}
                            <div class="promo-card-periode">
                                📅 {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M') }} — {{ \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') }}
                            </div>

                            {{-- Status --}}
                            <span class="promo-card-status-badge">
                                <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#34d399"/></svg>
                                Disetujui Aktif
                            </span>

                            {{-- Catatan dari Manajer --}}
                            @if($promo->alasan_verifikasi)
                            <div class="promo-card-note" title="{{ $promo->alasan_verifikasi }}">
                                💬 {{ $promo->alasan_verifikasi }}
                            </div>
                            @endif

                            {{-- Diverifikasi oleh --}}
                            @if($promo->verifier)
                            <div class="promo-verified-label">✔ Diverifikasi Manajer</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="pos-grid" id="pos-grid-container">

                    @forelse($menus as $menu)
                        @php
                            $icons = ['Makanan' => '🍲', 'Minuman' => '🥤', 'Cemilan' => '🍟', 'Snack' => '🥐'];
                            $icon = $icons[$menu->category] ?? '🍽️';
                        @endphp
                        <div class="pos-card {{ $menu->stock <= 0 ? 'out-of-stock' : '' }}"
                             data-category="{{ $menu->category }}"
                             data-name="{{ strtolower($menu->name) }}"
                             onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, {{ $menu->stock }})">
                            
                            @if($menu->stock <= 0)
                                <span class="badge-habis">Habis</span>
                            @endif

                            <div>
                                {{-- Gambar menu atau emoji fallback --}}
                                @if($menu->image)
                                    <div class="pos-card-img-wrap">
                                        <img src="{{ asset('storage/' . $menu->image) }}"
                                             alt="{{ $menu->name }}"
                                             class="pos-card-img"
                                             loading="lazy">
                                    </div>
                                @else
                                    <div class="pos-card-icon">{{ $icon }}</div>
                                @endif
                                <div class="pos-card-category">{{ $menu->category }}</div>
                                <div class="pos-card-title">{{ $menu->name }}</div>
                            </div>

                            <div class="pos-card-footer">
                                <div class="pos-card-price">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                                <div class="pos-card-stock">Stok: {{ $menu->stock }}</div>
                            </div>
                        </div>
                    @empty
                        <p style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 40px;">
                            Belum ada master data menu yang aktif.
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Right: Dark Modern Cart Sidebar -->
            <aside class="pos-cart-sidebar">
                <div class="cart-header-box">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <h3 style="font-size: 1.1rem; font-weight: 700;">Pesanan Saat Ini</h3>
                    </div>
                    <button onclick="clearCart()" style="background: none; border: none; color: #f87171; cursor: pointer; font-size: 0.82rem; font-weight: 600;">Kosongkan</button>
                </div>

                <div class="cart-items-scroll" id="cart-scroll-items">
                    <!-- Dynamic Vanilla JS Cart Items -->
                </div>

                <!-- Checkout Panel -->
                <div class="cart-checkout-box">
                    <div class="summary-line">
                        <span>Total Jumlah Item</span>
                        <strong id="cart-total-qty" style="color: #ffffff;">0</strong>
                    </div>
                    <div class="summary-line grand-total">
                        <span>Grand Total</span>
                        <span class="grand-total-val" id="cart-grand-total">Rp 0</span>
                    </div>

                    <!-- Payment Mode Selector -->
                    <div class="pay-toggle-box">
                        <div class="pay-toggle-btn active" id="tab-pay-cash" onclick="setPaymentMode('cash')">Tunai (Cash)</div>
                        <div class="pay-toggle-btn" id="tab-pay-qris" onclick="setPaymentMode('qris')">QRIS</div>
                    </div>

                    <!-- Cash Input Area -->
                    <div class="cash-box-inner" id="cash-panel-section">
                        <input type="number" id="cash-given-input" class="cash-input-field" placeholder="Nominal Uang (Rp)" oninput="calcDynamicChange()">
                        
                        <div class="quick-cash-row">
                            <button type="button" class="quick-pill" onclick="applyQuickCash('exact')">Uang Pas</button>
                            <button type="button" class="quick-pill" onclick="applyQuickCash(50000)">50.000</button>
                            <button type="button" class="quick-pill" onclick="applyQuickCash(100000)">100.000</button>
                            <button type="button" class="quick-pill" onclick="applyQuickCash(200000)">200.000</button>
                        </div>

                        <div class="change-result-box">
                            <span>Kembalian:</span>
                            <span id="cash-change-label">Rp 0</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" class="btn-order-checkout" id="btn-execute-checkout" onclick="initiateCheckout()" disabled>
                        Proses & Cetak Struk
                    </button>
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- Modal Popup Dummy QRIS -->
<div class="modal-glass-backdrop" id="modal-qris">
    <div class="modal-glass-card">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: #ffffff;">Scan Pembayaran QRIS</h3>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">NMID: ID1020304050607</p>

        <div class="qr-render-frame">
            <svg viewBox="0 0 100 100" width="180" height="180">
                <path fill="#000" d="M0,0 h30 v30 h-30 z M5,5 v20 h20 v-20 z M10,10 h10 v10 h-10 z" />
                <path fill="#000" d="M70,0 h30 v30 h-30 z M75,5 v20 h20 v-20 z M80,10 h10 v10 h-10 z" />
                <path fill="#000" d="M0,70 h30 v30 h-30 z M5,75 v20 h20 v-20 z M10,80 h10 v10 h-10 z" />
                <rect x="35" y="10" width="5" height="15" fill="#000" />
                <rect x="45" y="5" width="15" height="5" fill="#000" />
                <rect x="45" y="15" width="5" height="20" fill="#000" />
                <rect x="55" y="20" width="10" height="5" fill="#000" />
                <rect x="35" y="40" width="20" height="5" fill="#000" />
                <rect x="10" y="35" width="15" height="5" fill="#000" />
                <rect x="20" y="45" width="10" height="15" fill="#000" />
                <rect x="40" y="50" width="15" height="15" fill="#000" />
                <rect x="65" y="45" width="10" height="25" fill="#000" />
                <rect x="80" y="40" width="15" height="10" fill="#000" />
                <rect x="80" y="60" width="10" height="15" fill="#000" />
                <rect x="35" y="70" width="15" height="10" fill="#000" />
                <rect x="40" y="85" width="25" height="10" fill="#000" />
                <rect x="75" y="85" width="15" height="10" fill="#000" />
            </svg>
        </div>

        <p style="font-size: 0.85rem; color: var(--text-secondary);">Total Tagihan QRIS:</p>
        <div id="qris-charge-amount" style="font-size: 1.5rem; font-weight: 800; color: #60a5fa; margin: 4px 0 18px;">Rp 0</div>

        <button type="button" class="btn-order-checkout" onclick="confirmQrisAndSave()">
            Konfirmasi Pembayaran
        </button>
        <button type="button" onclick="closeQrisModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 0.85rem; margin-top: 12px;">
            Batal
        </button>
    </div>
</div>

<!-- Format Struk Kasir Thermal Printer (58mm) -->
<div id="pos-thermal-receipt">
    <div class="pr-center">
        <h3 class="pr-bold" style="font-size: 13px;">RESTO NUSANTARA POS</h3>
        <p>Jl. Sudirman No. 88, Jakarta</p>
        <p>Telp: (021) 555-8899</p>
    </div>
    <div class="pr-divider"></div>
    <div class="pr-flex"><span>Invoice:</span><span id="prt-inv" class="pr-bold">-</span></div>
    <div class="pr-flex"><span>Waktu:</span><span id="prt-time">-</span></div>
    <div class="pr-flex"><span>Kasir:</span><span id="prt-cashier">-</span></div>
    <div class="pr-divider"></div>
    <div id="prt-items-box"></div>
    <div class="pr-divider"></div>
    <div class="pr-flex pr-bold"><span>TOTAL:</span><span id="prt-total">Rp 0</span></div>
    <div class="pr-flex"><span>METODE:</span><span id="prt-method">-</span></div>
    <div class="pr-flex"><span>BAYAR:</span><span id="prt-paid">Rp 0</span></div>
    <div class="pr-flex"><span>KEMBALI:</span><span id="prt-change">Rp 0</span></div>
    <div class="pr-divider"></div>
    <div class="pr-center" style="margin-top: 6px;">
        <p class="pr-bold">TERIMA KASIH</p>
        <p>Silakan Berkunjung Kembali!</p>
    </div>
</div>

<!-- Engine Vanilla JavaScript POS -->
<script>
    let posCart = [];
    let activePaymentMode = 'cash';
    let currentGrandTotal = 0;
    let selectedCategoryFilter = 'All';

    const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);

    // Kategori Filter
    function selectCategory(category, element) {
        selectedCategoryFilter = category;
        document.querySelectorAll('.pill-filter-btn').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        applyMenuFilters();
    }

    // Live Search Filter
    function filterMenuSearch() {
        applyMenuFilters();
    }

    function applyMenuFilters() {
        const query = document.getElementById('menu-search-input').value.toLowerCase().trim();
        document.querySelectorAll('.pos-card').forEach(card => {
            const cardCat = card.dataset.category;
            const cardName = card.dataset.name;
            const matchCategory = (selectedCategoryFilter === 'All' || cardCat === selectedCategoryFilter);
            const matchSearch = (!query || cardName.includes(query));

            card.style.display = (matchCategory && matchSearch) ? 'flex' : 'none';
        });
    }

    // Tambah Item ke Keranjang
    function addToCart(id, name, price, stock) {
        if (stock <= 0) return;

        const found = posCart.find(item => item.id === id);
        if (found) {
            if (found.qty + 1 > stock) {
                alert(`Stok '${name}' tersisa ${stock}`);
                return;
            }
            found.qty += 1;
        } else {
            posCart.push({ id, name, price, stock, qty: 1 });
        }
        refreshCartUI();
    }

    // Ubah Kuantitas
    function adjustQty(id, delta) {
        const item = posCart.find(i => i.id === id);
        if (!item) return;

        if (item.qty + delta > item.stock) {
            alert(`Stok maksimal '${item.name}' tercapai.`);
            return;
        }

        item.qty += delta;
        if (item.qty <= 0) {
            posCart = posCart.filter(i => i.id !== id);
        }
        refreshCartUI();
    }

    // Kosongkan Keranjang
    function clearCart() {
        if (posCart.length === 0) return;
        if (confirm('Kosongkan seluruh pesanan di keranjang?')) {
            posCart = [];
            refreshCartUI();
        }
    }

    // Pilih Mode Pembayaran
    function setPaymentMode(mode) {
        activePaymentMode = mode;
        document.getElementById('tab-pay-cash').classList.toggle('active', mode === 'cash');
        document.getElementById('tab-pay-qris').classList.toggle('active', mode === 'qris');
        document.getElementById('cash-panel-section').style.display = (mode === 'cash') ? 'block' : 'none';
        validateOrderButton();
    }

    // Tombol Uang Pas & Cepat
    function applyQuickCash(nominal) {
        const input = document.getElementById('cash-given-input');
        if (nominal === 'exact') {
            input.value = currentGrandTotal;
        } else {
            input.value = nominal;
        }
        calcDynamicChange();
    }

    // Kalkulasi Kembalian Dinamis
    function calcDynamicChange() {
        if (activePaymentMode !== 'cash') return;
        const paid = parseFloat(document.getElementById('cash-given-input').value) || 0;
        const change = paid - currentGrandTotal;
        document.getElementById('cash-change-label').innerText = formatRupiah(change > 0 ? change : 0);
        validateOrderButton();
    }

    // Validasi Tombol Bayar
    function validateOrderButton() {
        const btn = document.getElementById('btn-execute-checkout');
        if (posCart.length === 0) {
            btn.disabled = true;
            return;
        }

        if (activePaymentMode === 'cash') {
            const paid = parseFloat(document.getElementById('cash-given-input').value) || 0;
            btn.disabled = (paid < currentGrandTotal || currentGrandTotal <= 0);
        } else {
            btn.disabled = (currentGrandTotal <= 0);
        }
    }

    // Render Ulang Keranjang
    function refreshCartUI() {
        const box = document.getElementById('cart-scroll-items');
        box.innerHTML = '';
        currentGrandTotal = 0;
        let totalCount = 0;

        if (posCart.length === 0) {
            box.innerHTML = `
                <div class="cart-empty-state">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <p style="margin-top: 10px; font-weight: 600;">Keranjang Belum Berisi</p>
                    <small style="color: var(--text-muted);">Klik menu di sebelah kiri untuk menambah pesanan</small>
                </div>
            `;
        }

        posCart.forEach(item => {
            const subtotal = item.price * item.qty;
            currentGrandTotal += subtotal;
            totalCount += item.qty;

            const row = document.createElement('div');
            row.className = 'cart-row';
            row.innerHTML = `
                <div>
                    <div class="cart-row-title">${item.name}</div>
                    <div class="cart-row-price">${formatRupiah(item.price)} × ${item.qty} = <strong style="color:#60a5fa;">${formatRupiah(subtotal)}</strong></div>
                </div>
                <div class="cart-qty-pill">
                    <button type="button" class="btn-qty-mini" onclick="adjustQty(${item.id}, -1)">−</button>
                    <span class="qty-val-label">${item.qty}</span>
                    <button type="button" class="btn-qty-mini" onclick="adjustQty(${item.id}, 1)">+</button>
                </div>
            `;
            box.appendChild(row);
        });

        document.getElementById('cart-total-qty').innerText = totalCount;
        document.getElementById('cart-grand-total').innerText = formatRupiah(currentGrandTotal);

        calcDynamicChange();
        validateOrderButton();
    }

    // Checkout Flow
    function initiateCheckout() {
        if (activePaymentMode === 'qris') {
            document.getElementById('qris-charge-amount').innerText = formatRupiah(currentGrandTotal);
            document.getElementById('modal-qris').style.display = 'flex';
        } else {
            sendCheckoutRequest();
        }
    }

    function closeQrisModal() {
        document.getElementById('modal-qris').style.display = 'none';
    }

    function confirmQrisAndSave() {
        closeQrisModal();
        sendCheckoutRequest();
    }

    // Submit ke Server via AJAX / Fetch
    async function sendCheckoutRequest() {
        const btn = document.getElementById('btn-execute-checkout');
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        const paid = activePaymentMode === 'cash' 
            ? parseFloat(document.getElementById('cash-given-input').value)
            : currentGrandTotal;

        const payload = {
            payment_method: activePaymentMode,
            paid_amount: paid,
            items: posCart.map(i => ({ id: i.id, qty: i.qty }))
        };

        try {
            const res = await fetch("{{ route('pos.checkout') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();

            if (!res.ok || !result.success) {
                throw new Error(result.message || 'Transaksi gagal diproses.');
            }

            // Print Receipt
            renderAndPrintReceipt(result.data);

            // Reset Cart
            posCart = [];
            document.getElementById('cash-given-input').value = '';
            document.getElementById('cash-change-label').innerText = 'Rp 0';
            refreshCartUI();

        } catch (err) {
            alert('Kesalahan Transaksi: ' + err.message);
        } finally {
            btn.innerText = 'Proses & Cetak Struk';
            validateOrderButton();
        }
    }

    // Struk Thermal Printer
    function renderAndPrintReceipt(trx) {
        document.getElementById('prt-inv').innerText = trx.invoice_number;
        document.getElementById('prt-time').innerText = new Date(trx.created_at).toLocaleString('id-ID');
        document.getElementById('prt-cashier').innerText = trx.cashier ? trx.cashier.name : 'Kasir';

        const itemsEl = document.getElementById('prt-items-box');
        itemsEl.innerHTML = '';
        trx.details.forEach(item => {
            const row = document.createElement('div');
            row.className = 'pr-flex';
            row.innerHTML = `<span>${item.menu_name} ×${item.quantity}</span><span>${formatRupiah(item.subtotal)}</span>`;
            itemsEl.appendChild(row);
        });

        document.getElementById('prt-total').innerText = formatRupiah(trx.total_amount);
        document.getElementById('prt-method').innerText = trx.payment_method.toUpperCase();
        document.getElementById('prt-paid').innerText = formatRupiah(trx.paid_amount);
        document.getElementById('prt-change').innerText = formatRupiah(trx.change_amount);

        // Cetak struk otomatis
        window.print();
    }

    // Inisialisasi awal
    refreshCartUI();
</script>

</body>
</html>
