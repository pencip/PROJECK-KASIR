@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru - Admin')
@section('header-title', 'Tambah Pengguna')

@section('content')
<div class="card-dark" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title-lg">Tambah Akun Pengguna</h3>
            <p class="card-subtitle">Buat akun untuk kasir, manajer, atau administrator sistem.</p>
        </div>
        <a href="{{ route('manager.users.index') }}" class="btn-dark-pill">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('manager.users.store') }}" method="POST">
        @csrf
        <div class="form-group-dark">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control-dark" placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" required>
        </div>

        <div class="form-group-dark">
            <label>Alamat Email</label>
            <input type="email" name="email" class="form-control-dark" placeholder="nama@resto.com" value="{{ old('email') }}" required>
        </div>

        <div class="form-group-dark">
            <label>Kata Sandi</label>
            <input type="password" name="password" class="form-control-dark" placeholder="Minimal 6 karakter" required>
        </div>

        <div class="form-group-dark">
            <label>Peran / Role</label>
            <select name="role" class="form-control-dark" required>
                <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir (Hanya Akses Operasional POS)</option>
                <option value="manajer" {{ old('role') === 'manajer' ? 'selected' : '' }}>Manajer (Akses Dashboard Analitik & Laporan)</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh Seluruh Sistem)</option>
            </select>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn-white-pill" style="width: 100%; padding: 13px;">
                Simpan Akun Pengguna
            </button>
        </div>
    </form>
</div>
@endsection
