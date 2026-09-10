@extends('layouts.app')

@section('title', 'Tambah Akun Pengguna — Admin')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.users.index') }}" style="color: var(--text-secondary); font-size: 13px; display: inline-flex; align-items: center; gap: 0.35rem; margin-bottom: 0.5rem;">
            &larr; Kembali ke Daftar Pengguna
        </a>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Tambah Akun Pengguna</h1>
        <p style="color: var(--text-secondary); font-size: 13px;">Buat akun pengguna baru dengan role 'admin' atau 'user' (FR-05, FC-03).</p>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Formulir Akun Baru</div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap <span style="color: var(--error);">*</span></label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name') }}" 
                        placeholder="Contoh: John Doe" 
                        required 
                        autofocus
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email <span style="color: var(--error);">*</span></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        value="{{ old('email') }}" 
                        placeholder="nama@domain.com" 
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">Email bersifat unik dan digunakan untuk identitas login serta kolaborasi.</div>
                </div>

                <div class="form-group">
                    <label for="role" class="form-label">Role Akun <span style="color: var(--error);">*</span></label>
                    <select 
                        id="role" 
                        name="role" 
                        class="form-control @error('role') is-invalid @enderror" 
                        required
                    >
                        <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>User (Pengguna Reguler)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Administrator Sistem)</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">Admin dapat mengelola seluruh akun pengguna di sistem.</div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password <span style="color: var(--error);">*</span></label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Minimal 8 karakter" 
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span style="color: var(--error);">*</span></label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-control" 
                        placeholder="Ulangi password di atas" 
                        required
                    >
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--divider);">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
