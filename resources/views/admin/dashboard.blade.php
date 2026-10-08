@extends('layouts.app')

@section('title', 'Dashboard - Admin')
@section('header-title', 'Dashboard Admin')

@section('styles')
<style>
/* Same base variables/design system as manager dashboard */
.page-header-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 22px; }
.page-welcome h2 { font-size: 1.35rem; font-weight: 800; color: #ffffff; letter-spacing: -0.3px; margin: 0; }
.page-welcome p { font-size: 0.8rem; color: var(--text-muted); margin-top: 3px; }

.highlights-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
@media (max-width: 1100px) { .highlights-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .highlights-grid { grid-template-columns: 1fr; } }

.hl-card { background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 14px; padding: 18px 20px; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.hl-label { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-bottom: 12px; }
.hl-value { font-size: 1.6rem; font-weight: 800; color: #ffffff; line-height: 1.1; }
.hl-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; }

.main-section { display: grid; grid-template-columns: 1fr 300px; gap: 16px; align-items: stretch; margin-bottom: 24px;}
@media (max-width: 1000px) { .main-section { grid-template-columns: 1fr; } }

.tbl-card, .dc-card { background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 14px; padding: 20px 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
.tbl-hdr { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.tbl-title { font-size: 1.05rem; font-weight: 700; color: #ffffff; }
.tbl-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }

.m-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.m-table thead th { padding: 12px 16px; font-size: 0.72rem; font-weight: 600; color: var(--text-muted); text-align: left; background: var(--bg-surface); }
.m-table tbody td { padding: 14px 16px; font-size: 0.8rem; color: var(--text-secondary); border-bottom: 1px solid var(--border-subtle); }
.sdot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; }

.tbtn { font-size: 0.75rem; font-weight: 500; padding: 6px 14px; border-radius: 8px; border: 1px solid var(--border-card); background: var(--bg-surface); color: var(--text-secondary); text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px; }
.tbtn:hover { background: rgba(255,255,255,0.05); color: #ffffff; }
.tbtn.accent { background: #2563eb; color: #fff; border-color: #2563eb; }
.tbtn.accent:hover { background: #1d4ed8; }

.dc-title { font-size: 0.9rem; font-weight: 700; color: #ffffff; margin-bottom: 16px; }
</style>
@endsection

@section('content')

<div class="page-header-row">
    <div class="page-welcome">
        <h2>Selamat Datang, {{ auth()->user()->name }}! 🛠️</h2>
        <p>Ringkasan sistem & master data — {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
</div>

<div class="highlights-grid">
    <div class="hl-card">
        <div class="hl-label">👥 Total Karyawan</div>
        <div class="hl-value">{{ $totalUsers }}</div>
        <div class="hl-sub">{{ $kasirCount }} Kasir &middot; {{ $managerCount }} Manajer</div>
    </div>
    <div class="hl-card">
        <div class="hl-label">🍔 Total Menu Aktif</div>
        <div class="hl-value">{{ $activeMenus }}</div>
        <div class="hl-sub">Dari total {{ $totalMenus }} menu di database</div>
    </div>
    <div class="hl-card">
        <div class="hl-label" style="color: #ef4444;">⚠️ Peringatan Stok</div>
        <div class="hl-value" style="color: #ef4444;">{{ $outOfStockCount }}</div>
        <div class="hl-sub">Menu kosong. {{ $lowStockCount }} menu mau habis!</div>
    </div>
    <div class="hl-card">
        <div class="hl-label">🏷️ Promo Sistem</div>
        <div class="hl-value">{{ $totalPromos }}</div>
        <div class="hl-sub">{{ $pendingPromos }} Menunggu Persetujuan</div>
    </div>
</div>

<div class="main-section" style="align-items: flex-start;">
    {{-- Left: Low Stock Warnings --}}
    <div class="tbl-card" style="margin-bottom: 0;">
        <div class="tbl-hdr">
            <div>
                <div class="tbl-title">Menu Stok Menipis & Kosong</div>
                <div class="tbl-sub">Prioritas update stok bahan/menu hari ini</div>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="m-table">
                <thead>
                    <tr>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok Tersisa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockMenus as $menu)
                        <tr>
                            <td style="color:#fff; font-weight:600;">{{ $menu->name }}</td>
                            <td>{{ $menu->category }}</td>
                            <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                            <td>
                                @if($menu->stock <= 0)
                                    <span style="color:#ef4444; font-weight:700; background:rgba(239,68,68,0.1); padding:2px 8px; border-radius:4px;">Kosong!</span>
                                @else
                                    <span style="color:#f59e0b; font-weight:700;">{{ $menu->stock }} Porsi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:30px; color:var(--text-muted);">
                                ✅ Semua stok menu aman!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right: Actions & Role Info --}}
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="dc-card" style="display:flex; flex-direction:column; justify-content:space-between;">
            <div>
                <div class="dc-title">Aksi Cepat Admin</div>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:20px;">Pintasan master data</div>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom:16px;">
                    <a href="{{ route('admin.menus.create') }}" class="tbtn" style="flex-direction:column; padding:12px; gap:8px;">
                        <span style="font-size:1.2rem;">🍔</span> Tambah Menu
                    </a>
                    <a href="{{ route('admin.promotions.create') }}" class="tbtn" style="flex-direction:column; padding:12px; gap:8px;">
                        <span style="font-size:1.2rem;">🎟️</span> Buat Promo
                    </a>
                    <a href="{{ route('admin.menus.index') }}" class="tbtn" style="flex-direction:column; padding:12px; gap:8px;">
                        <span style="font-size:1.2rem;">📋</span> Master Data
                    </a>
                </div>
            </div>
        </div>
        
        <div class="dc-card">
            <div class="dc-title" style="margin-bottom:8px;">Registrasi Karyawan Terkini</div>
            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach($recentUsers as $user)
                    <div style="display:flex; align-items:center; justify-content:space-between; padding-bottom:8px; border-bottom:1px solid var(--border-subtle);">
                        <div>
                            <div style="color:#fff; font-size:0.8rem; font-weight:600;">{{ $user->name }}</div>
                            <div style="color:var(--text-muted); font-size:0.7rem;">{{ $user->email }}</div>
                        </div>
                        <span style="font-size:0.65rem; padding:3px 8px; border-radius:12px; background:rgba(255,255,255,0.05); text-transform:uppercase;">
                            {{ $user->role }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
