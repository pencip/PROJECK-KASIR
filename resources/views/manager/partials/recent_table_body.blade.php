@php
    $dotColors = ['#f59e0b','#60a5fa','#34d399','#a78bfa','#f43f5e','#06b6d4','#fbbf24','#fb7185'];
@endphp
@forelse($recentTransactions as $idx => $trx)
    @php $method = strtolower($trx->payment_method); @endphp
    <tr>
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
