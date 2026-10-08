@extends('layouts.app')

@section('title', 'Master Data Menu - Admin')
@section('header-title', 'Master Data Menu')

@section('content')
<div class="card-dark">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title-lg">Katalog Master Data Menu</h3>
            <p class="card-subtitle">Kelola daftar menu makanan, minuman, harga, dan ketersediaan stok kasir.</p>
        </div>
        <a href="{{ route('admin.menus.create') }}" class="btn-white-pill">
            + Tambah Menu Baru
        </a>
    </div>

    <div class="dark-table-container">
        <table class="table-dark">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga Satuan</th>
                    <th>Sisa Stok</th>
                    <th>Status POS</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $index => $menu)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 700;">{{ $menus->firstItem() + $index }}</td>
                        <td>
                            <strong style="color: #ffffff; font-size: 0.95rem;">{{ $menu->name }}</strong>
                        </td>
                        <td>
                            <span style="background-color: var(--bg-pill); border: 1px solid var(--border-subtle); color: var(--text-secondary); padding: 3px 10px; border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 600;">
                                {{ $menu->category }}
                            </span>
                        </td>
                        <td style="color: #60a5fa; font-weight: 800;">
                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                        </td>
                        <td>
                            <span style="font-weight: 800; color: {{ $menu->stock <= 5 ? '#f87171' : '#34d399' }};">
                                {{ $menu->stock }} unit
                            </span>
                        </td>
                        <td>
                            <span style="background: {{ $menu->is_active ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)' }}; color: {{ $menu->is_active ? '#34d399' : '#f87171' }}; padding: 3px 10px; border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700;">
                                {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.menus.edit', $menu) }}" class="btn-dark-pill" style="padding: 4px 12px; font-size: 0.75rem;">
                                    Edit
                                </a>
                                <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Hapus menu {{ $menu->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-dark-pill" style="padding: 4px 12px; font-size: 0.75rem; color: #f87171; border-color: rgba(239, 68, 68, 0.3);">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Belum ada master data menu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $menus->links() }}
    </div>
</div>
@endsection
