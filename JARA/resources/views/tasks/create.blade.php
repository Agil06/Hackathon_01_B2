{{-- 
    FR-10: Membuat tugas di dalam daftar tugas
    BR-03: Title max 255
    BR-04: Priority low/medium/high (default medium), deadline valid date opsional
--}}
@extends('layouts.app')

@section('title', 'Buat Task Baru')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('projects.show', $project) }}" style="color: var(--text-muted); font-size: 13px;">&larr; Kembali ke Daftar Tugas</a>
</div>

<div class="card" style="max-width: 640px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Buat Task Baru</h1>
        <p style="color: var(--text-muted); font-size: 13px;">
            Daftar Tugas: <strong>{{ $project->name }}</strong>
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terdapat kesalahan pengisian formulir:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tasks.store', $project) }}">
        @csrf

        <div class="form-group">
            <label for="title" class="form-label">Judul Task <span class="required">*</span></label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                class="form-control @error('title') is-invalid @enderror" 
                value="{{ old('title') }}" 
                required 
                autofocus
                maxlength="255"
                placeholder="Masukkan judul task"
            >
            @error('title')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="priority" class="form-label">Prioritas <span class="required">*</span></label>
                <select 
                    id="priority" 
                    name="priority" 
                    class="form-select @error('priority') is-invalid @enderror"
                    required
                >
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium (Default)</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                </select>
                @error('priority')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="deadline" class="form-label">Tenggat Waktu (Opsional)</label>
                <input 
                    type="date" 
                    id="deadline" 
                    name="deadline" 
                    class="form-control @error('deadline') is-invalid @enderror" 
                    value="{{ old('deadline') }}"
                >
                @error('deadline')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Hook Assignees untuk Abhi (FR-14) --}}
        @if (View::exists('tasks._assignees'))
            @include('tasks._assignees', ['project' => $project, 'task' => null])
        @endif

        <div class="form-actions" style="border-top: 1px solid var(--border); padding-top: 1.25rem;">
            <button type="submit" class="btn btn-primary">Simpan Task</button>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection