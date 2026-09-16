{{-- 
    Partial untuk menampilkan daftar task di project detail
    Digunakan oleh Project Detail view (milik al)
    FR-11, FR-12 (mark-done), FR-13 (delete)
--}}

@php
    $taskList = isset($tasks) ? $tasks : $project->tasks;
@endphp

@if ($taskList->count() > 0)
    <div class="card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Judul Task</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tenggat Waktu</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($taskList as $task)
                        <tr>
                            <td>
                                <a href="{{ route('tasks.show', [$project, $task]) }}" class="task-link">
                                    {{ $task->title }}
                                </a>
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
                                    {{ $task->deadline ? $task->deadline->format('d M Y') : '—' }}
                                </span>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; gap: 0.35rem; align-items: center; justify-content: flex-end;">
                                    @if ($task->status !== 'done')
                                        <form method="POST" action="{{ route('tasks.done', [$project, $task]) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-primary" title="Tandai Selesai" style="padding: 2px 8px; font-size: 11px;">
                                                ✓ Selesai
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('tasks.edit', [$project, $task]) }}" class="btn btn-sm btn-secondary" style="padding: 2px 8px; font-size: 11px;">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('tasks.destroy', [$project, $task]) }}" 
                                        style="display: inline;" 
                                        onsubmit="return confirm('Hapus task ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 2px 8px; font-size: 11px;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="card" style="text-align: center; padding: 3rem 1.5rem; margin-bottom: 1.5rem;">
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Belum ada task di daftar tugas ini.</p>
        <a href="{{ route('tasks.create', $project) }}" class="btn btn-primary btn-sm">
            + Buat Task Pertama
        </a>
    </div>
@endif