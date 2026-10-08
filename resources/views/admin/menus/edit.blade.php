@extends('layouts.app')

@section('title', 'Edit Menu - Admin')
@section('header-title', 'Edit Menu')

@section('content')
<div class="card-dark" style="max-width: 640px; margin: 0 auto;">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title-lg">Edit Menu: {{ $menu->name }}</h3>
            <p class="card-subtitle">Perbarui informasi harga, nama, stok, atau gambar menu.</p>
        </div>
        <a href="{{ route('admin.menus.index') }}" class="btn-dark-pill">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('admin.menus.update', $menu) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group-dark">
            <label>Nama Menu</label>
            <input type="text" name="name" class="form-control-dark" value="{{ old('name', $menu->name) }}" required>
        </div>

        <div class="form-group-dark">
            <label>Kategori</label>
            <input type="text" name="category" class="form-control-dark" value="{{ old('category', $menu->category) }}" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group-dark">
                <label>Harga Jual (Rp)</label>
                <input type="number" name="price" class="form-control-dark" min="0" value="{{ old('price', $menu->price) }}" required>
            </div>

            <div class="form-group-dark">
                <label>Sisa Stok</label>
                <input type="number" name="stock" class="form-control-dark" min="0" value="{{ old('stock', $menu->stock) }}" required>
            </div>
        </div>

        {{-- Gambar Menu --}}
        <div class="form-group-dark">
            <label>Gambar Menu <span style="color: var(--text-muted); font-weight: 400; text-transform: none;">(opsional, maks. 2MB)</span></label>

            {{-- Gambar saat ini --}}
            @if($menu->image)
            <div id="current-img-box" style="margin-bottom: 12px;">
                <p style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 6px;">Gambar saat ini:</p>
                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                     style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);"
                     id="current-img-display">
                <label style="display: flex; align-items: center; gap: 8px; margin-top: 8px; cursor: pointer;">
                    <input type="checkbox" name="remove_image" value="1" id="chk-remove-img"
                           style="width: 16px; height: 16px; accent-color: #ef4444;"
                           onchange="toggleRemoveImage(this)">
                    <span style="color: #f87171; font-size: 0.82rem;">Hapus gambar ini</span>
                </label>
            </div>
            @endif

            {{-- Preview gambar baru --}}
            <div id="img-preview-box" style="display: none; margin-bottom: 12px;">
                <p style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 6px;">Gambar baru (preview):</p>
                <img id="img-preview" src="" alt="Preview"
                     style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                <button type="button" onclick="clearImagePreview()"
                        style="display: block; margin-top: 8px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; padding: 5px 14px; border-radius: 20px; font-size: 0.78rem; cursor: pointer;">
                    ✕ Batal Ganti Gambar
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
                <span style="color: var(--text-secondary); font-size: 0.88rem;">
                    {{ $menu->image ? 'Klik untuk ganti gambar' : 'Klik untuk upload gambar atau drag & drop' }}
                </span>
                <span style="color: var(--text-muted); font-size: 0.75rem;">JPG, PNG, WebP — maks. 2MB</span>
            </label>
            <input type="file" id="image-upload" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                   style="display: none;" onchange="previewImage(this)">
            @error('image')
                <p style="color: #f87171; font-size: 0.8rem; margin-top: 6px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group-dark" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
            <input type="checkbox" id="is_active" name="is_active" value="1" {{ $menu->is_active ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--accent-blue);">
            <label for="is_active" style="margin-bottom: 0; cursor: pointer; text-transform: none; color: #ffffff;">
                Status Aktif di Halaman POS
            </label>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn-white-pill" style="width: 100%; padding: 13px;">
                Perbarui Perubahan Menu
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
        // Uncheck remove image if uploading new
        const chk = document.getElementById('chk-remove-img');
        if (chk) chk.checked = false;
        const curBox = document.getElementById('current-img-box');
        if (curBox) curBox.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function clearImagePreview() {
    document.getElementById('image-upload').value = '';
    document.getElementById('img-preview').src = '';
    document.getElementById('img-preview-box').style.display = 'none';
    document.getElementById('img-upload-label').style.display = 'flex';
    const curBox = document.getElementById('current-img-box');
    if (curBox) curBox.style.display = 'block';
}

function toggleRemoveImage(checkbox) {
    const curImg = document.getElementById('current-img-display');
    if (curImg) {
        curImg.style.opacity = checkbox.checked ? '0.3' : '1';
        curImg.style.filter = checkbox.checked ? 'grayscale(1)' : 'none';
    }
    // Hide upload area if removing
    if (checkbox.checked) {
        document.getElementById('img-upload-label').style.display = 'none';
    } else {
        document.getElementById('img-upload-label').style.display = 'flex';
    }
}
</script>
@endsection
