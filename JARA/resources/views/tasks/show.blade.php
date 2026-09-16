{{-- 
    FR-11: Melihat detail task
    FR-12: Aksi menandai selesai
    FR-13: Menghapus task
--}}
@extends('layouts.app')

@section('title', 'Detail Task — ' . $task->title)

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('projects.show', $project) }}" style="color: var(--text-muted); font-size: 13px;">&larr; Kembali ke Daftar Tugas</a>
</div>

<div class="card" style="max-width: 720px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; margin-bottom: 0.5rem; word-break: break-word;">{{ $task->title }}</h1>
            <p style="color: var(--text-muted); font-size: 13px;">
                Daftar Tugas: <a href="{{ route('projects.show', $project) }}" style="font-weight: 500;">{{ $project->name }}</a>
            </p>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-shrink: 0; align-items: center;">
            <span class="badge badge-priority-{{ $task->priority }}">
                {{ ucfirst($task->priority) }}
            </span>
            <span class="badge badge-status-{{ $task->status }}">
                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
            </span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; background-color: var(--recessed); padding: 1rem; border-radius: var(--radius-md);">
        <div>
            <span style="font-size: 12px; color: var(--text-muted); display: block; text-transform: uppercase; letter-spacing: 0.04em;">Tenggat Waktu</span>
            <strong style="font-size: 14px; color: var(--text-main);">
                {{ $task->deadline ? $task->deadline->format('d M Y') : 'Tidak ada deadline' }}
            </strong>
        </div>
        <div>
            <span style="font-size: 12px; color: var(--text-muted); display: block; text-transform: uppercase; letter-spacing: 0.04em;">Dibuat</span>
            <span style="font-size: 13px; color: var(--text-main);">
                {{ $task->created_at->format('d M Y, H:i') }}
            </span>
        </div>
        <div>
            <span style="font-size: 12px; color: var(--text-muted); display: block; text-transform: uppercase; letter-spacing: 0.04em;">Terakhir Diperbarui</span>
            <span style="font-size: 13px; color: var(--text-main);">
                {{ $task->updated_at->format('d M Y, H:i') }}
            </span>
        </div>
    </div>

    {{-- Hook Detail Assignees untuk Abhi (FR-15) --}}
    @if (View::exists('tasks._assignees_list'))
        <div style="margin-bottom: 1.5rem;">
            @include('tasks._assignees_list', ['project' => $project, 'task' => $task])
        </div>
    @elseif (View::exists('tasks._show_assignees'))
        <div style="margin-bottom: 1.5rem;">
            @include('tasks._show_assignees', ['project' => $project, 'task' => $task])
        </div>
    @endif

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; border-top: 1px solid var(--border); padding-top: 1.25rem;">
        @if ($task->status !== 'done')
            <form method="POST" action="{{ route('tasks.done', [$project, $task]) }}" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-primary">
                    ✓ Tandai Selesai
                </button>
            </form>
        @endif

        <a href="{{ route('tasks.edit', [$project, $task]) }}" class="btn btn-secondary">
            Edit Task
        </a>

        <form method="POST" action="{{ route('tasks.destroy', [$project, $task]) }}" style="display: inline;" 
            onsubmit="return confirm('Apakah Anda yakin ingin menghapus task ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                Hapus Task
            </button>
        </form>

        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary" style="margin-left: auto;">
            Kembali
        </a>
    </div>
</div>
@endsection
