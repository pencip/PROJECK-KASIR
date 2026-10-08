@extends('layouts.app')
@section('title', 'Edit Promosi - Admin')
@section('header-title', 'Edit Promosi')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-secondary col-lg-8 mx-auto" style="background:rgba(255,255,255,0.03)">
        <div class="card-header border-secondary d-flex align-items-center gap-2" style="background:rgba(255,255,255,0.05)">
            <i class="bi bi-pencil-square text-warning"></i>
            <h5 class="mb-0 text-light">Edit Promosi: <span class="text-warning">{{ $promotion->nama_promo }}</span></h5>
        </div>
        <div class="card-body py-4">

            {{-- Info status saat ini --}}
            <div class="mb-4 p-3 rounded" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div>
                        <small class="text-muted d-block">Status Saat Ini</small>
                        @if($promotion->status === 'approved')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                        @elseif($promotion->status === 'rejected')
                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi</span>
                        @endif
                    </div>
                    @if($promotion->verifier)
                    <div>
                        <small class="text-muted d-block">Diverifikasi Oleh</small>
                        <span class="text-light fw-semibold">{{ $promotion->verifier->name }}</span>
                    </div>
                    @endif
                    @if($promotion->alasan_verifikasi)
                    <div>
                        <small class="text-muted d-block">Catatan Manajer</small>
                        <span class="text-light">{{ $promotion->alasan_verifikasi }}</span>
                    </div>
                    @endif
                </div>
                @if(in_array($promotion->status, ['approved', 'rejected']))
                <div class="alert alert-warning bg-warning bg-opacity-10 border-warning border-opacity-25 text-warning mt-3 mb-0" style="font-size:0.82rem;">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Jika Anda menyimpan perubahan, status akan dikembalikan ke <strong>Pending</strong> dan promosi ini akan diajukan ulang ke Manajer untuk diverifikasi kembali.
                </div>
                @endif
            </div>

            <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Nama Promosi --}}
                <div class="mb-3">
                    <label class="form-label text-light fw-semibold">Nama Promosi <span class="text-danger">*</span></label>
                    <input type="text" name="nama_promo"
                           value="{{ old('nama_promo', $promotion->nama_promo) }}"
                           class="form-control bg-dark text-light border-secondary @error('nama_promo') is-invalid @enderror"
                           required>
                    @error('nama_promo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Gambar Produk --}}
                <div class="mb-3">
                    <label class="form-label text-light fw-semibold">Gambar Produk Makanan/Minuman</label>

                    {{-- Gambar saat ini --}}
                    @if($promotion->gambar)
                    <div class="mb-2 d-flex align-items-center gap-3">
                        <img src="{{ asset('storage/' . $promotion->gambar) }}"
                             alt="{{ $promotion->nama_promo }}"
                             style="width:90px;height:70px;object-fit:cover;border-radius:10px;border:2px solid rgba(96,165,250,0.4);"
                             id="current-img">
                        <div>
                            <small class="text-muted d-block mb-1">Gambar saat ini</small>
                            <small class="text-info">Upload gambar baru untuk mengganti</small>
                        </div>
                    </div>
                    @endif

                    <input type="file" name="gambar" id="input-gambar"
                           class="form-control bg-dark text-light border-secondary @error('gambar') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                           onchange="previewGambar(this)">
                    <small class="text-muted">Format: JPG, PNG, GIF, WebP. Maks 2MB. Kosongkan jika tidak ingin mengganti gambar.</small>
                    @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    {{-- Preview gambar baru --}}
                    <div id="preview-container" class="mt-2" style="display:none;">
                        <small class="text-success d-block mb-1"><i class="bi bi-arrow-right-circle me-1"></i>Gambar baru:</small>
                        <img id="preview-img" src="" alt="Preview Baru" style="max-width:160px; max-height:120px; border-radius:10px; border:2px solid rgba(52,211,153,0.4);">
                    </div>
                </div>

                {{-- Tipe & Nilai Diskon --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-light fw-semibold">Tipe Diskon <span class="text-danger">*</span></label>
                        <select name="tipe_diskon" class="form-select bg-dark text-light border-secondary @error('tipe_diskon') is-invalid @enderror" required>
                            <option value="persen" {{ old('tipe_diskon', $promotion->tipe_diskon) === 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="nominal" {{ old('tipe_diskon', $promotion->tipe_diskon) === 'nominal' ? 'selected' : '' }}>Nominal (Rupiah)</option>
                        </select>
                        @error('tipe_diskon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light fw-semibold">Nilai Diskon <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="nilai_diskon"
                               value="{{ old('nilai_diskon', $promotion->nilai_diskon) }}"
                               class="form-control bg-dark text-light border-secondary @error('nilai_diskon') is-invalid @enderror"
                               required>
                        @error('nilai_diskon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Periode Promo --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-light fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai"
                               value="{{ old('tanggal_mulai', $promotion->tanggal_mulai) }}"
                               class="form-control bg-dark text-light border-secondary @error('tanggal_mulai') is-invalid @enderror" required>
                        @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_selesai"
                               value="{{ old('tanggal_selesai', $promotion->tanggal_selesai) }}"
                               class="form-control bg-dark text-light border-secondary @error('tanggal_selesai') is-invalid @enderror" required>
                        @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-warning text-dark fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewGambar(input) {
        const container = document.getElementById('preview-container');
        const img = document.getElementById('preview-img');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            container.style.display = 'none';
        }
    }
</script>
@endsection
