{{-- 
    FR-12: Mengubah judul, prioritas, tenggat waktu, dan status tugas
    BR-03: Title max 255
    BR-04: Priority low/medium/high
    BR-05: Status not_done/in_progress/done (semua perpindahan status diizinkan)
--}}
@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('projects.show', $project) }}" style="color: var(--text-muted); font-size: 13px;">&larr; Kembali ke Daftar Tugas</a>
</div>

<div class="card" style="max-width: 640px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Edit Task</h1>
        <p style="color: var(--text-muted); font-size: 13px;">
            Daftar Tugas: <strong>{{ $project->name }}</strong> &middot; Task: <strong>{{ $task->title }}</strong>
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

    <form method="POST" action="{{ route('tasks.update', [$project, $task]) }}">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="title" class="form-label">Judul Task <span class="required">*</span></label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                class="form-control @error('title') is-invalid @enderror" 
                value="{{ old('title', $task->title) }}" 
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
                    @foreach ($priorities as $priority)
                        <option value="{{ $priority }}" {{ old('priority', $task->priority) === $priority ? 'selected' : '' }}>
                            {{ ucfirst($priority) }}
                        </option>
                    @endforeach
                </select>
                @error('priority')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status <span class="required">*</span></label>
                <select 
                    id="status" 
                    name="status" 
                    class="form-select @error('status') is-invalid @enderror"
                    required
                >
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ old('status', $task->status) === $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="deadline" class="form-label">Tenggat Waktu</label>
            <input 
                type="date" 
                id="deadline" 
                name="deadline" 
                class="form-control @error('deadline') is-invalid @enderror" 
                value="{{ old('deadline', $task->deadline?->format('Y-m-d')) }}"
            >
            @error('deadline')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Hook Assignees untuk Abhi (FR-14, FR-15) --}}
        @if (View::exists('tasks._assignees'))
            @include('tasks._assignees', ['project' => $project, 'task' => $task])
        @endif

        <div class="form-actions" style="border-top: 1px solid var(--border); padding-top: 1.25rem;">
            <button type="submit" class="btn btn-primary">Update Task</button>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection