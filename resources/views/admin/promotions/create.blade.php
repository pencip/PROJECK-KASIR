@extends('layouts.app')
@section('title', 'Ajukan Promosi Baru - Admin')
@section('header-title', 'Ajukan Promosi Baru')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-secondary col-lg-8 mx-auto" style="background:rgba(255,255,255,0.03)">
        <div class="card-header border-secondary d-flex align-items-center gap-2" style="background:rgba(255,255,255,0.05)">
            <i class="bi bi-tag-fill text-primary"></i>
            <h5 class="mb-0 text-light">Ajukan Promosi Baru ke Manajer</h5>
        </div>
        <div class="card-body py-4">
            <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-info mb-4" role="alert" style="font-size:0.85rem;">
                <i class="bi bi-info-circle me-2"></i>
                Setelah diajukan, promosi akan menunggu <strong>persetujuan Manajer</strong>. Jika disetujui, promosi akan otomatis tampil di halaman <strong>Kasir POS</strong>.
            </div>

            <form action="{{ route('admin.promotions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Nama Promosi --}}
                <div class="mb-3">
                    <label class="form-label text-light fw-semibold">Nama Promosi <span class="text-danger">*</span></label>
                    <input type="text" name="nama_promo" value="{{ old('nama_promo') }}"
                           class="form-control bg-dark text-light border-secondary @error('nama_promo') is-invalid @enderror"
                           placeholder="cth: Paket Hemat Nasi Goreng, Diskon Weekend..." required>
                    @error('nama_promo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Upload Gambar Produk --}}
                <div class="mb-3">
                    <label class="form-label text-light fw-semibold">Gambar Produk Makanan/Minuman</label>
                    <input type="file" name="gambar" id="input-gambar"
                           class="form-control bg-dark text-light border-secondary @error('gambar') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                           onchange="previewGambar(this)">
                    <small class="text-muted">Format: JPG, PNG, GIF, WebP. Maks 2MB.</small>
                    @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    {{-- Preview Gambar --}}
                    <div id="preview-container" class="mt-2" style="display:none;">
                        <img id="preview-img" src="" alt="Preview" style="max-width:160px; max-height:120px; border-radius:10px; border:2px solid rgba(96,165,250,0.4);">
                    </div>
                </div>

                {{-- Tipe & Nilai Diskon --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-light fw-semibold">Tipe Diskon <span class="text-danger">*</span></label>
                        <select name="tipe_diskon" class="form-select bg-dark text-light border-secondary @error('tipe_diskon') is-invalid @enderror" required>
                            <option value="persen" {{ old('tipe_diskon') === 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="nominal" {{ old('tipe_diskon') === 'nominal' ? 'selected' : '' }}>Nominal (Rupiah)</option>
                        </select>
                        @error('tipe_diskon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light fw-semibold">Nilai Diskon <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="nilai_diskon" value="{{ old('nilai_diskon') }}"
                               class="form-control bg-dark text-light border-secondary @error('nilai_diskon') is-invalid @enderror"
                               placeholder="cth: 10 (untuk 10%) atau 5000" required>
                        @error('nilai_diskon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Periode Promo --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-light fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                               class="form-control bg-dark text-light border-secondary @error('tanggal_mulai') is-invalid @enderror" required>
                        @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                               class="form-control bg-dark text-light border-secondary @error('tanggal_selesai') is-invalid @enderror" required>
                        @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Simpan & Ajukan ke Manajer
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
