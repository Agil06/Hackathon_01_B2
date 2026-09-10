@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('projects.index') }}" style="color: var(--text-muted); font-size: 13px;">&larr; Back to Projects</a>
        <h1 style="font-size: 1.5rem; margin-top: 0.5rem;">Create New Project</h1>
        <p style="color: var(--text-muted); font-size: 13px;">You will automatically become a member of this project.</p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Project Name</label>
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
                <button type="submit" class="btn btn-primary">Create Project</button>
            </div>
        </form>
    </div>
</div>
@endsection
