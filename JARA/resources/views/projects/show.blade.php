@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('projects.index') }}" style="color: var(--text-muted); font-size: 13px;">&larr; Kembali ke Daftar Tugas</a>
</div>

<!-- Daftar Tugas Header Card -->
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.75rem;">{{ $project->name }}</h1>
                @php
                    $progressClass = match($project->progress) {
                        'Completed' => 'badge-progress-completed',
                        'In Progress' => 'badge-progress-in-progress',
                        default => 'badge-progress-not-started',
                    };
                @endphp
                <span class="badge {{ $progressClass }}" style="font-size: 12px; padding: 4px 10px;">{{ $project->progress }}</span>
            </div>
            <p style="color: var(--text-muted); font-size: 13px;">
                Owned by <strong style="color: var(--text-main);">{{ $project->owner->name ?? 'Unknown' }}</strong> ({{ $project->owner->email ?? '' }})
                &bull; {{ $project->created_at->format('M d, Y') }}
            </p>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary btn-sm">Ubah Daftar Tugas</a>
            @endcan
            @can('delete', $project)
                <form method="POST" action="{{ route('projects.destroy', $project) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this project? All associated tasks and memberships will be permanently deleted.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Hapus Daftar Tugas</button>
                </form>
            @endcan
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
    <!-- Tasks Section -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.25rem;">Tasks ({{ $project->tasks->count() }})</h2>
            @if(Route::has('tasks.create'))
                <a href="{{ route('tasks.create', $project) }}" class="btn btn-primary btn-sm">+ Add Task</a>
            @endif
        </div>

        @if(View::exists('projects._task-list'))
            @include('projects._task-list', ['project' => $project, 'tasks' => $project->tasks])
        @else
            @if($project->tasks->isEmpty())
                <div class="card" style="text-align: center; padding: 3rem 1.5rem;">
                    <p style="color: var(--text-muted); margin-bottom: 1rem;">No tasks yet in this project.</p>
                    @if(Route::has('tasks.create'))
                        <a href="{{ route('tasks.create', $project) }}" class="btn btn-secondary btn-sm">Add First Task</a>
                    @endif
                </div>
            @else
                <div class="card" style="padding: 0; overflow: hidden;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                @if(Route::has('tasks.show'))
                                    <th style="text-align: right;">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->tasks as $task)
                                <tr>
                                    <td>
                                        @if(Route::has('tasks.show'))
                                            <a href="{{ route('tasks.show', [$project, $task]) }}" style="font-weight: 500; color: var(--text-main);">
                                                {{ $task->title }}
                                            </a>
                                        @else
                                            <span style="font-weight: 500;">{{ $task->title }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-priority-{{ $task->priority }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $task->status }}">
                                            {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="color: var(--text-muted); font-size: 13px;">
                                            {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('M d, Y') : '—' }}
                                        </span>
                                    </td>
                                    @if(Route::has('tasks.show'))
                                        <td style="text-align: right;">
                                            <a href="{{ route('tasks.show', [$project, $task]) }}" class="btn btn-secondary btn-sm">View</a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </div>

    <!-- Members Section & Collaborators -->
    <div>
        <div style="margin-bottom: 1rem;">
            <h2 style="font-size: 1.25rem;">Anggota ({{ $project->members->count() }})</h2>
        </div>

        <div class="card" style="margin-bottom: 1.5rem;">
            <ul style="list-style: none;">
                @foreach($project->members as $member)
                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0; border-bottom: 1px solid var(--border);">
                        <div>
                            <div style="font-weight: 500; font-size: 13px; color: var(--text-main);">{{ $member->name }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $member->email }}</div>
                        </div>
                        @if($member->id === $project->owner_id)
                            <span class="badge" style="background-color: var(--primary); color: #ffffff;">Owner</span>
                        @else
                            @can('manageMembers', $project)
                                <form method="POST" action="{{ route('collaborators.destroy', [$project, $member]) }}" onsubmit="return confirm('Remove this member from the project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            @else
                                <span class="badge" style="background-color: var(--recessed); color: var(--text-muted);">Member</span>
                            @endcan
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Add Collaborator Form (abhi's feature) -->
        @can('manageMembers', $project)
            @if(View::exists('projects._collaborator-form'))
            @include('projects._collaborator-form', ['project' => $project])
            @endif
        @endcan
    </div>
</div>
@endsection
