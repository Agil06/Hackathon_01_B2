@extends('layouts.app')

@section('title', 'Tugas Saya')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Tugas Saya</h1>
        <p class="page-subtitle">Task yang ditugaskan kepada Anda.</p>
    </div>

    <div class="card">
        <div class="card-body">
            @forelse ($tasks as $task)
                <div class="task-list-item">
                    <a class="task-link" href="{{ route('tasks.show', [$task->project, $task]) }}">{{ $task->title }}</a>
                    <span class="badge badge-priority badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                    <span class="badge badge-status badge-{{ $task->status }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                    <span class="text-muted">Project: {{ $task->project->name }}</span>
                </div>
            @empty
                <p class="empty-state-text">Belum ada task yang ditugaskan kepada Anda.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
