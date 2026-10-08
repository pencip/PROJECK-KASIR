@extends('layouts.app')
@section('title', 'Verifikasi Promosi - Manajer')
@section('header-title', 'Verifikasi Pengajuan Promosi')

@section('content')
<div class="container-fluid px-0">

    <div class="mb-4">
        <h4 class="fw-bold text-light mb-1">Verifikasi Pengajuan Promosi</h4>
        <small class="text-muted">Setujui atau tolak promosi yang diajukan Admin. Promosi yang disetujui akan otomatis tampil di halaman Kasir POS.</small>
    </div>

    {{-- ===== PENDING: Perlu Tindakan ===== --}}
    <div class="card border-secondary mb-4" style="background:rgba(255,255,255,0.03)">
        <div class="card-header border-secondary d-flex align-items-center gap-2" style="background:rgba(245,158,11,0.12)">
            <i class="bi bi-hourglass-split text-warning"></i>
            <span class="fw-semibold text-warning">Menunggu Tindakan Anda ({{ $pendingPromotions->count() }} Pengajuan)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead style="border-bottom:2px solid rgba(255,255,255,0.1)">
                        <tr>
                            <th style="width:80px;">Gambar</th>
                            <th>Diajukan Oleh</th>
                            <th>Nama Promo</th>
                            <th>Diskon</th>
                            <th>Periode</th>
                            <th>Aksi & Catatan Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingPromotions as $promo)
                        <tr>
                            <td>
                                @if($promo->gambar)
                                    <img src="{{ asset('storage/' . $promo->gambar) }}"
                                         alt="{{ $promo->nama_promo }}"
                                         width="64" height="64"
                                         style="object-fit:cover; border-radius:10px; border:2px solid rgba(255,255,255,0.12); cursor:pointer;"
                                         onclick="showImageModal('{{ asset('storage/' . $promo->gambar) }}', '{{ $promo->nama_promo }}')"
                                         title="Klik untuk memperbesar">
                                @else
                                    <div style="width:64px;height:64px;border-radius:10px;background:rgba(255,255,255,0.07);display:flex;align-items:center;justify-content:center;color:#6b7280;">
                                        <i class="bi bi-image fs-4"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-light">{{ $promo->creator->name ?? 'Admin' }}</div>
                                <small class="text-muted">{{ $promo->created_at->format('d M Y, H:i') }}</small>
                            </td>
                            <td class="fw-semibold text-light">{{ $promo->nama_promo }}</td>
                            <td>
                                @if($promo->tipe_diskon === 'persen')
                                    <span class="badge" style="background:rgba(96,165,250,0.15);color:#60a5fa;font-size:0.85rem;">{{ $promo->nilai_diskon }}%</span>
                                @else
                                    <span class="badge" style="background:rgba(52,211,153,0.15);color:#34d399;font-size:0.85rem;">Rp {{ number_format($promo->nilai_diskon, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td style="font-size:0.83rem; color:#a0aec0;">
                                {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }}
                                <span style="color:#4a5568;"> s/d </span>
                                {{ \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td>
                                <form action="{{ route('manager.promotions.verify', $promo->id) }}" method="POST">
                                    @csrf
                                    <div class="d-flex gap-2 align-items-center flex-wrap">
                                        <input type="text" name="alasan_verifikasi"
                                               placeholder="Catatan / alasan (opsional)..."
                                               class="form-control form-control-sm bg-dark text-light border-secondary"
                                               style="min-width:200px; max-width:280px;">
                                        <button type="submit" name="action" value="approved"
                                                class="btn btn-sm btn-success text-nowrap">
                                            <i class="bi bi-check-lg me-1"></i> Setujui
                                        </button>
                                        <button type="submit" name="action" value="rejected"
                                                class="btn btn-sm btn-danger text-nowrap">
                                            <i class="bi bi-x-lg me-1"></i> Tolak
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-check-all fs-2 d-block mb-2" style="opacity:0.3;color:#34d399;"></i>
                                Tidak ada promosi yang menunggu persetujuan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== HISTORY: Riwayat Verifikasi ===== --}}
    <div class="card border-secondary" style="background:rgba(255,255,255,0.03)">
        <div class="card-header border-secondary d-flex align-items-center gap-2" style="background:rgba(255,255,255,0.05)">
            <i class="bi bi-clock-history text-secondary"></i>
            <span class="fw-semibold text-secondary">Riwayat Verifikasi (20 Terakhir)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead style="border-bottom:2px solid rgba(255,255,255,0.1)">
                        <tr>
                            <th style="width:70px;">Gambar</th>
                            <th>Diajukan Oleh</th>
                            <th>Nama Promo</th>
                            <th>Diskon</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Diverifikasi Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyPromotions as $promo)
                        <tr>
                            <td>
                                @if($promo->gambar)
                                    <img src="{{ asset('storage/' . $promo->gambar) }}"
                                         alt="{{ $promo->nama_promo }}"
                                         width="54" height="54"
                                         style="object-fit:cover; border-radius:8px; border:2px solid rgba(255,255,255,0.08); cursor:pointer; opacity:{{ $promo->status === 'rejected' ? '0.5' : '1' }};"
                                         onclick="showImageModal('{{ asset('storage/' . $promo->gambar) }}', '{{ $promo->nama_promo }}')"
                                         title="Klik untuk memperbesar">
                                @else
                                    <div style="width:54px;height:54px;border-radius:8px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;color:#4a5568;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td style="font-size:0.83rem;">{{ $promo->creator->name ?? 'Admin' }}</td>
                            <td class="fw-semibold" style="{{ $promo->status === 'rejected' ? 'opacity:0.6;' : '' }}">{{ $promo->nama_promo }}</td>
                            <td>
                                @if($promo->tipe_diskon === 'persen')
                                    <span style="color:#60a5fa;font-size:0.85rem;">{{ $promo->nilai_diskon }}%</span>
                                @else
                                    <span style="color:#34d399;font-size:0.85rem;">Rp {{ number_format($promo->nilai_diskon, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td style="font-size:0.82rem; color:#9ca3af;">
                                {{ \Carbon\Carbon::parse($promo->tanggal_mulai)->format('d M Y') }}
                                <br>
                                {{ \Carbon\Carbon::parse($promo->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td>
                                @if($promo->status === 'approved')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                                @endif
                            </td>
                            <td style="font-size:0.82rem; color:#9ca3af; max-width:160px;">{{ $promo->alasan_verifikasi ?? '-' }}</td>
                            <td style="font-size:0.82rem; color:#9ca3af;">{{ $promo->verifier->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat verifikasi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Preview Gambar --}}
<div id="image-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(8px); z-index:9999; align-items:center; justify-content:center;" onclick="closeImageModal()">
    <div style="text-align:center;" onclick="event.stopPropagation()">
        <p id="modal-img-title" style="color:#e2e8f0; font-weight:700; margin-bottom:12px; font-size:1.1rem;"></p>
        <img id="modal-img" src="" alt="" style="max-width:85vw; max-height:75vh; border-radius:16px; border:2px solid rgba(255,255,255,0.15); box-shadow:0 20px 60px rgba(0,0,0,0.8);">
        <p style="color:#6b7280; margin-top:12px; font-size:0.82rem;">Klik di luar gambar untuk menutup</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showImageModal(src, title) {
        document.getElementById('modal-img').src = src;
        document.getElementById('modal-img-title').textContent = title;
        const modal = document.getElementById('image-modal');
        modal.style.display = 'flex';
    }
    function closeImageModal() {
        document.getElementById('image-modal').style.display = 'none';
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeImageModal();
    });
</script>
@endsection
