@extends('layouts.app')

@section('title', 'Daftar Pengguna — Admin')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Manajemen Pengguna</h1>
        <p style="color: var(--text-secondary); font-size: 13px;">Kelola akun pengguna dan hak akses sistem (FR-04, FR-05, FR-06).</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Tambah Akun Baru
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">Daftar Akun Pengguna</div>
        <span style="font-size: 12px; color: var(--text-secondary);">Total: {{ $users->count() }} Pengguna</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Terdaftar Pada</th>
                    <th style="text-align: right; width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="color: var(--text-secondary); font-weight: 500;">#{{ $user->id }}</td>
                        <td>
                            <strong style="color: var(--text-main);">{{ $user->name }}</strong>
                            @if(auth()->id() === $user->id)
                                <span style="font-size: 11px; margin-left: 0.5rem; color: var(--indigo); font-weight: 600;">(Anda)</span>
                            @endif
                        </td>
                        <td style="color: var(--text-secondary);">{{ $user->email }}</td>
                        <td>
                            <span class="role-badge {{ $user->role }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td style="color: var(--text-secondary);">
                            {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td style="text-align: right;">
                            @if(auth()->id() === $user->id)
                                <span style="font-size: 12px; color: var(--text-secondary); font-style: italic;">Akun Aktif</span>
                            @else
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->email }} secara permanen? Seluruh project ciptaannya akan terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            Tidak ada data pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
