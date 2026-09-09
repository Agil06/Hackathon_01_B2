{{-- 
    FR-14: Membuat task
    FC-06: Field title, priority, deadline (status tidak ada di create)
--}}
@extends('layouts.app')

@section('title', 'Buat Task Baru')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Buat Task Baru</h1>
        <p class="page-subtitle">Untuk project: <strong>{{ $project->name }}</strong></p>
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

            <form method="POST" action="{{ route('tasks.store', $project) }}" class="form">
                @csrf

                <div class="form-group">
                    <label for="title" class="form-label">Title Task <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-input @error('title') is-invalid @enderror" 
                        value="{{ old('title') }}" 
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
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="deadline" class="form-label">Deadline</label>
                        <input 
                            type="date" 
                            id="deadline" 
                            name="deadline" 
                            class="form-input @error('deadline') is-invalid @enderror" 
                            value="{{ old('deadline') }}"
                        >
                        @error('deadline')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Task</button>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection