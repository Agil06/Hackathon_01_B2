@extends('layouts.app')

@section('title', 'Buat Daftar Tugas')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('projects.index') }}" style="color: var(--text-muted); font-size: 13px;">&larr; Kembali ke Daftar Tugas</a>
        <h1 style="font-size: 1.5rem; margin-top: 0.5rem;">Buat Daftar Tugas Baru</h1>
        <p style="color: var(--text-muted); font-size: 13px;">Anda otomatis menjadi owner dan anggota daftar tugas ini.</p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nama Daftar Tugas</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    class="form-control" 
                    value="{{ old('name') }}" 
                    required 
                    maxlength="255"
                    placeholder="e.g. Website Redesign"
                    autofocus
                >
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Buat Daftar Tugas</button>
            </div>
        </form>
    </div>
</div>
@endsection
