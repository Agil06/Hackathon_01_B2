@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="projects-hero">
    <div>
        <span class="eyebrow">WORKSPACE</span>
        <h1>Projects</h1>
        <p>Semua pekerjaan tim Anda, tersusun rapi dalam satu tempat.</p>
    </div>
    @can('create', App\Models\Project::class)
        <a href="{{ route('projects.create') }}" class="btn btn-primary">+ Create Project</a>
    @endcan
</div>

@if($projects->isEmpty())
    <div class="card empty-projects">
        <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem; color: var(--text-main);">No projects yet</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You are not a member of any projects. Create your first project to get started.</p>
        @can('create', App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="btn btn-primary">Create Your First Project</a>
        @endcan
    </div>
@else
    <div class="card project-table-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Progress</th>
                    <th>Tasks</th>
                    <th>Creator</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                    <tr>
                        <td>
                            <a href="{{ route('projects.show', $project) }}" class="project-name">
                                {{ $project->name }}
                            </a>
                        </td>
                        <td>
                            @php
                                $progressClass = match($project->progress) {
                                    'Completed' => 'badge-progress-completed',
                                    'In Progress' => 'badge-progress-in-progress',
                                    default => 'badge-progress-not-started',
                                };
                            @endphp
                            <span class="badge {{ $progressClass }}">{{ $project->progress }}</span>
                        </td>
                        <td>
                            <span style="color: var(--text-muted); font-size: 13px;">{{ $project->tasks->count() }} tasks</span>
                        </td>
                        <td>
                            <span style="color: var(--text-muted); font-size: 13px;">{{ $project->creator->name ?? 'Unknown' }}</span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-sm">View</a>
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('projects.destroy', $project) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this project? All associated tasks will be permanently removed.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
