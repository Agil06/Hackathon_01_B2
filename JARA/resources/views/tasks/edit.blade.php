{{-- 
    FR-16: Mengubah task
    FC-07: Field title, priority, deadline, status
--}}
@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Edit Task</h1>
        <p class="page-subtitle">
            Project: <strong>{{ $project->name }}</strong> &middot;
            Task: <strong>{{ $task->title }}</strong>
        </p>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-error">
                    <ul class="alert-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tasks.update', [$project, $task]) }}" class="form">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="title" class="form-label">Title Task <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-input @error('title') is-invalid @enderror" 
                        value="{{ old('title', $task->title) }}" 
                        required 
                        autofocus
                        placeholder="Masukkan judul task"
                    >
                    @error('title')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="priority" class="form-label">Priority <span class="required">*</span></label>
                        <select 
                            id="priority" 
                            name="priority" 
                            class="form-select @error('priority') is-invalid @enderror"
                            required
                        >
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority }}" {{ old('priority', $task->priority) == $priority ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $priority)) }}
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
                                <option value="{{ $status }}" {{ old('status', $task->status) == $status ? 'selected' : '' }}>
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
                    <label for="deadline" class="form-label">Deadline</label>
                    <input 
                        type="date" 
                        id="deadline" 
                        name="deadline" 
                        class="form-input @error('deadline') is-invalid @enderror" 
                        value="{{ old('deadline', $task->deadline?->format('Y-m-d')) }}"
                    >
                    @error('deadline')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection