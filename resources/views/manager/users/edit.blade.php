@extends('layouts.app')

@section('title', 'Edit Pengguna - Admin')
@section('header-title', 'Edit Pengguna')

@section('content')
<div class="card-dark" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title-lg">Edit Pengguna: {{ $user->name }}</h3>
            <p class="card-subtitle">Perbarui data login atau hak akses peran pengguna.</p>
        </div>
        <a href="{{ route('manager.users.index') }}" class="btn-dark-pill">
            &larr; Kembali
        </a>
    </div>

    <form action="{{ route('manager.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group-dark">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control-dark" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group-dark">
            <label>Alamat Email</label>
            <input type="email" name="email" class="form-control-dark" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group-dark">
            <label>Kata Sandi Baru (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" name="password" class="form-control-dark" placeholder="Masukkan sandi baru jika ingin diubah">
        </div>

        <div class="form-group-dark">
            <label>Peran / Role</label>
            <select name="role" class="form-control-dark" required>
                <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir (Hanya Akses Operasional POS)</option>
                <option value="manajer" {{ old('role', $user->role) === 'manajer' ? 'selected' : '' }}>Manajer (Akses Dashboard Analitik & Laporan)</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh Seluruh Sistem)</option>
            </select>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn-white-pill" style="width: 100%; padding: 13px;">
                Simpan Perubahan Pengguna
            </button>
        </div>
    </form>
</div>
@endsection
