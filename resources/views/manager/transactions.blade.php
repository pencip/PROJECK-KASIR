@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Manajer')
@section('header-title', 'Riwayat Transaksi')

@section('content')
<div class="card-dark">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title-lg">Laporan Riwayat Transaksi Penjualan</h3>
            <p class="card-subtitle">Filter dan analisis rincian transaksi kasir.</p>
        </div>
        <a href="{{ route('manager.dashboard') }}" class="btn-dark-pill">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('manager.transactions') }}" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; background-color: var(--bg-surface); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
        <div style="flex: 1; min-width: 150px;">
            <label style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 6px; text-transform: uppercase;">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control-dark" style="padding: 8px 12px;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 6px; text-transform: uppercase;">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control-dark" style="padding: 8px 12px;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 6px; text-transform: uppercase;">Metode Pembayaran</label>
            <select name="payment_method" class="form-control-dark" style="padding: 8px 12px;">
                <option value="">Semua Metode</option>
                <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                <option value="qris" {{ request('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
            </select>
        </div>
        <div style="align-self: flex-end; display: flex; gap: 8px;">
            <button type="submit" class="btn-white-pill" style="padding: 9px 18px;">Filter</button>
            <a href="{{ route('manager.transactions') }}" class="btn-dark-pill" style="padding: 9px 18px;">Reset</a>
        </div>
    </form>

    <div class="dark-table-container">
        <table class="table-dark">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Item & Menu</th>
                    <th>Metode</th>
                    <th>Total Belanja</th>
                    <th>Dibayar</th>
                    <th>Kembalian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    <tr>
                        <td>
                            <strong style="color: #ffffff; letter-spacing: 0.5px;">{{ $trx->invoice_number }}</strong>
                        </td>
                        <td style="color: var(--text-secondary); font-size: 0.82rem;">
                            {{ $trx->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <div class="avatar-circle" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                    {{ strtoupper(substr($trx->cashier ? $trx->cashier->name : 'K', 0, 1)) }}
                                </div>
                                <span>{{ $trx->cashier ? $trx->cashier->name : '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <details style="cursor: pointer;">
                                <summary style="color: #60a5fa; font-weight: 600; font-size: 0.85rem;">
                                    {{ $trx->details->sum('quantity') }} Item Terjual &darr;
                                </summary>
                                <div style="margin-top: 8px; padding: 10px; background-color: var(--bg-surface); border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); font-size: 0.8rem;">
                                    @foreach($trx->details as $item)
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px; color: var(--text-secondary);">
                                            <span>• {{ $item->menu_name }} ({{ $item->quantity }}x)</span>
                                            <span style="color: #ffffff; font-weight: 600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        </td>
                        <td>
                            <span style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: var(--radius-pill); text-transform: uppercase;">
                                {{ $trx->payment_method }}
                            </span>
                        </td>
                        <td style="font-weight: 800; color: #34d399; font-size: 0.95rem;">
                            Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                        </td>
                        <td style="color: var(--text-secondary);">
                            Rp {{ number_format($trx->paid_amount, 0, ',', '.') }}
                        </td>
                        <td style="color: var(--text-muted);">
                            Rp {{ number_format($trx->change_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Tidak ditemukan data transaksi yang sesuai filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
