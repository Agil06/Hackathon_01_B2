@extends('layouts.app')

@section('title', 'Ubah Daftar Tugas — ' . $project->name)

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('projects.show', $project) }}" style="color: var(--text-muted); font-size: 13px;">&larr; Kembali ke Detail Daftar Tugas</a>
        <h1 style="font-size: 1.5rem; margin-top: 0.5rem;">Ubah Daftar Tugas</h1>
        <p style="color: var(--text-muted); font-size: 13px;">Ubah nama daftar tugas ini.</p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('projects.update', $project) }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label for="name" class="form-label">Nama Daftar Tugas</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    class="form-control" 
                    value="{{ old('name', $project->name) }}" 
                    required 
                    maxlength="255"
                    autofocus
                >
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
