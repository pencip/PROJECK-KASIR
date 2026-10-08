@extends('layouts.app')

@section('title', 'Tambah Menu Baru - Admin')
@section('header-title', 'Tambah Menu')

@section('content')
<div class="card-dark" style="max-width: 640px; margin: 0 auto;">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title-lg">Tambah Menu Baru</h3>
            <p class="card-subtitle">Entri master makanan atau minuman baru ke sistem POS.</p>
        </div>
        <a href="{{ route('admin.menus.index') }}" class="btn-dark-pill">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group-dark">
            <label>Nama Menu</label>
            <input type="text" name="name" class="form-control-dark" placeholder="Contoh: Bebek Goreng Kremes" value="{{ old('name') }}" required>
        </div>

        <div class="form-group-dark">
            <label>Kategori</label>
            <input type="text" name="category" class="form-control-dark" placeholder="Contoh: Makanan, Minuman, Cemilan" value="{{ old('category') }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group-dark">
                <label>Harga Jual (Rp)</label>
                <input type="number" name="price" class="form-control-dark" min="0" placeholder="25000" value="{{ old('price') }}" required>
            </div>

            <div class="form-group-dark">
                <label>Stok Awal</label>
                <input type="number" name="stock" class="form-control-dark" min="0" placeholder="50" value="{{ old('stock', 20) }}" required>
            </div>
        </div>

        {{-- Upload Gambar Menu --}}
        <div class="form-group-dark">
            <label>Gambar Menu <span style="color: var(--text-muted); font-weight: 400; text-transform: none;">(opsional, maks. 2MB)</span></label>

            {{-- Preview Area --}}
            <div id="img-preview-box" style="display: none; margin-bottom: 12px;">
                <img id="img-preview" src="" alt="Preview"
                     style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                <button type="button" onclick="clearImagePreview()"
                        style="display: block; margin-top: 8px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; padding: 5px 14px; border-radius: 20px; font-size: 0.78rem; cursor: pointer;">
                    ✕ Hapus Pilihan Gambar
                </button>
            </div>

            {{-- Drop Zone / Upload Button --}}
            <label for="image-upload" id="img-upload-label"
                   style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;
                          border: 2px dashed rgba(255,255,255,0.15); border-radius: 12px; padding: 28px 16px;
                          cursor: pointer; transition: border-color 0.2s, background 0.2s;
                          background: rgba(255,255,255,0.03);"
                   onmouseover="this.style.borderColor='rgba(96,165,250,0.5)'; this.style.background='rgba(96,165,250,0.05)'"
                   onmouseout="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.background='rgba(255,255,255,0.03)'">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.35)" stroke-width="1.5">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <span style="color: var(--text-secondary); font-size: 0.88rem;">Klik untuk upload gambar atau drag & drop di sini</span>
                <span style="color: var(--text-muted); font-size: 0.75rem;">JPG, PNG, WebP — maks. 2MB</span>
            </label>
            <input type="file" id="image-upload" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                   style="display: none;" onchange="previewImage(this)">
            @error('image')
                <p style="color: #f87171; font-size: 0.8rem; margin-top: 6px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group-dark" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
            <input type="checkbox" id="is_active" name="is_active" value="1" checked style="width: 18px; height: 18px; accent-color: var(--accent-blue);">
            <label for="is_active" style="margin-bottom: 0; cursor: pointer; text-transform: none; color: #ffffff;">
                Aktifkan menu ini (Langsung tampil di kasir POS)
            </label>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn-white-pill" style="width: 100%; padding: 13px;">
                Simpan &amp; Rilis ke POS
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('img-preview').src = e.target.result;
        document.getElementById('img-preview-box').style.display = 'block';
        document.getElementById('img-upload-label').style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function clearImagePreview() {
    document.getElementById('image-upload').value = '';
    document.getElementById('img-preview').src = '';
    document.getElementById('img-preview-box').style.display = 'none';
    document.getElementById('img-upload-label').style.display = 'flex';
}
</script>
@endsection
