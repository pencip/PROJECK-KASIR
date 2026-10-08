@extends('layouts.app')
@section('title', 'Daftar Promosi - Admin')
@section('header-title', 'Kelola Promosi')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-light mb-1">Daftar Promosi yang Diajukan</h4>
            <small class="text-muted">Ajukan promo baru → Manajer verifikasi → Admin kelola data promosi yang disetujui</small>
        </div>
        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Ajukan Promosi Baru
        </a>
    </div>

    {{-- Legenda Status --}}
    <div class="d-flex gap-3 mb-4 flex-wrap">
        <span class="badge bg-warning text-dark px-3 py-2" style="font-size:0.78rem;"><i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi Manajer</span>
        <span class="badge bg-success px-3 py-2" style="font-size:0.78rem;"><i class="bi bi-check-circle me-1"></i>Disetujui — Tampil di Kasir POS</span>
        <span class="badge bg-danger px-3 py-2" style="font-size:0.78rem;"><i class="bi bi-x-circle me-1"></i>Ditolak — Edit & Ajukan Ulang</span>
    </div>

    <div class="card border-secondary" style="background:rgba(255,255,255,0.03)">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="table-dark" style="border-bottom:2px solid rgba(255,255,255,0.1)">
                        <tr>
                            <th style="width:80px;">Gambar</th>
                            <th>Nama Promo</th>
                            <th>Diskon</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Catatan Verifikasi</th>
                            <th>Diverifikasi Oleh</th>
                            <th class="text-center" style="width:130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promotions as $promo)
                        <tr>
                            <td>
                                @if($promo->gambar)
                                    <img src="{{ asset('storage/' . $promo->gambar) }}"
                                         alt="{{ $promo->nama_promo }}"
                                         width="60" height="60"
                                         style="object-fit:cover; border-radius:10px; border:2px solid rgba(255,255,255,0.1);">
                                @else
                                    <div style="width:60px;height:60px;border-radius:10px;background:rgba(255,255,255,0.07);display:flex;align-items:center;justify-content:center;color:#6b7280;">
                                        <i class="bi bi-image fs-4"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $promo->nama_promo }}</td>
                            <td>
                                @if($promo->tipe_diskon === 'persen')
                                    <span class="badge" style="background:rgba(96,165,250,0.15);color:#60a5fa;font-size:0.85rem;">{{ $promo->nilai_diskon }}%</span>
                                @else
                                    <span class="badge" style="background:rgba(52,211,153,0.15);color:#34d399;font-size:0.85rem;">Rp {{ number_format($promo->nilai_diskon, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td style="font-size:0.83rem;">
                                <span style="color:#a0aec0;">{{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }}</span>
                                <span style="color:#4a5568;"> s/d </span>
                                <span style="color:#a0aec0;">{{ \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') }}</span>
                            </td>
                            <td>
                                @if($promo->status === 'approved')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                                @elseif($promo->status === 'rejected')
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                @endif
                            </td>
                            <td style="font-size:0.82rem; color:#9ca3af; max-width:180px;">
                                {{ $promo->alasan_verifikasi ?? '-' }}
                            </td>
                            <td style="font-size:0.82rem; color:#9ca3af;">
                                {{ $promo->verifier->name ?? '-' }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- Tombol Edit: bisa diedit kapan saja --}}
                                    <a href="{{ route('admin.promotions.edit', $promo->id) }}"
                                       class="btn btn-sm btn-outline-info" title="Edit Promosi">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.promotions.destroy', $promo->id) }}" method="POST"
                                          onsubmit="return confirm('Hapus promosi \'{{ $promo->nama_promo }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Promosi">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-tag-fill fs-2 d-block mb-2" style="opacity:0.3;"></i>
                                Belum ada promosi yang dibuat. Klik <strong>Ajukan Promosi Baru</strong> untuk memulai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $promotions->links() }}
    </div>
</div>
@endsection
