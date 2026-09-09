{{-- 
    FR-15: Melihat detail task
--}}
@extends('layouts.app')

@section('title', 'Detail Task')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Detail Task</h1>
        <p class="page-subtitle">
            Project: <a href="{{ route('projects.show', $project) }}" class="link">{{ $project->name }}</a>
        </p>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="task-detail">
                <div class="task-detail-header">
                    <h2 class="task-detail-title">{{ $task->title }}</h2>
                    
                    <div class="task-detail-badges">
                        <span class="badge badge-priority badge-{{ $task->priority }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                        <span class="badge badge-status badge-{{ $task->status }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>

                <div class="task-detail-meta">
                    <div class="meta-item">
                        <span class="meta-label">Deadline:</span>
                        <span class="meta-value">
                            {{ $task->deadline ? $task->deadline->format('d M Y') : 'Tidak ada deadline' }}
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Dibuat:</span>
                        <span class="meta-value">{{ $task->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Terakhir diubah:</span>
                        <span class="meta-value">{{ $task->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>

                <div class="task-detail-actions">
                    <a href="{{ route('tasks.edit', [$project, $task]) }}" class="btn btn-primary">
                        Edit Task
                    </a>
                    
                    <form method="POST" action="{{ route('tasks.destroy', [$project, $task]) }}" class="d-inline" 
                        onsubmit="return confirm('Yakin ingin menghapus task ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus Task</button>
                    </form>

                    <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                        Kembali ke Project
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection