@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Admin')
@section('header-title', 'Manajemen Pengguna')

@section('content')
<div class="card-dark">
    <div class="card-header-flex">
        <div>
            <h3 class="card-title-lg">Daftar Pengguna & Hak Akses</h3>
            <p class="card-subtitle">Kelola akun Admin, Manajer, dan Kasir yang memiliki akses ke sistem.</p>
        </div>
        <a href="{{ route('manager.users.create') }}" class="btn-white-pill">
            + Tambah Pengguna Baru
        </a>
    </div>

    <div class="dark-table-container">
        <table class="table-dark">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Email Login</th>
                    <th>Peran / Role</th>
                    <th>Terdaftar Sejak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 700;">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <strong style="color: #ffffff;">{{ $user->name }}</strong>
                            </div>
                        </td>
                        <td style="color: var(--text-secondary);">{{ $user->email }}</td>
                        <td>
                            @php
                                $roleColors = [
                                    'admin' => ['bg' => 'rgba(239, 68, 68, 0.15)', 'text' => '#f87171', 'border' => 'rgba(239, 68, 68, 0.3)'],
                                    'manajer' => ['bg' => 'rgba(245, 158, 11, 0.15)', 'text' => '#fbbf24', 'border' => 'rgba(245, 158, 11, 0.3)'],
                                    'kasir' => ['bg' => 'rgba(59, 130, 246, 0.15)', 'text' => '#60a5fa', 'border' => 'rgba(59, 130, 246, 0.3)'],
                                ];
                                $c = $roleColors[$user->role] ?? $roleColors['kasir'];
                            @endphp
                            <span style="background: {{ $c['bg'] }}; border: 1px solid {{ $c['border'] }}; color: {{ $c['text'] }}; padding: 3px 10px; border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.82rem;">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('manager.users.edit', $user) }}" class="btn-dark-pill" style="padding: 4px 12px; font-size: 0.75rem;">
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('manager.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-dark-pill" style="padding: 4px 12px; font-size: 0.75rem; color: #f87171; border-color: rgba(239, 68, 68, 0.3);">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 24px;">
        {{ $users->links() }}
    </div>
</div>
@endsection
