{{-- 
    FR-01: Guest dapat membuka form registrasi
    FC-01: Field name, email, password, password_confirmation
--}}
@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h1 class="auth-title">Buat Akun</h1>
        <p class="auth-subtitle">Daftar untuk mulai mengelola project dan task</p>

        @if ($errors->any())
            <div class="alert alert-error">
                <ul class="alert-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-input @error('name') is-invalid @enderror" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus
                    placeholder="Masukkan nama lengkap"
                >
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input @error('email') is-invalid @enderror" 
                    value="{{ old('email') }}" 
                    required
                    placeholder="nama@email.com"
                >
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input @error('password') is-invalid @enderror" 
                    required
                    placeholder="Minimal 8 karakter"
                >
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-input" 
                    required
                    placeholder="Ulangi password"
                >
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                Daftar
            </button>
        </form>

        <p class="auth-footer">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="auth-link">Login di sini</a>
        </p>
    </div>
</div>
@endsection