{{-- 
    Partial untuk menampilkan daftar task di project detail
    Digunakan oleh Project Detail view (milik al)
--}}

@if ($project->tasks->count() > 0)
    <div class="task-list">
        <div class="task-list-header">
            <h3 class="task-list-title">Daftar Task</h3>
            <a href="{{ route('tasks.create', $project) }}" class="btn btn-sm btn-primary">
                + Tambah Task
            </a>
        </div>

        <div class="table-responsive">
            <table class="table task-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($project->tasks as $task)
                        <tr>
                            <td>
                                <a href="{{ route('tasks.show', [$project, $task]) }}" class="task-link">
                                    {{ $task->title }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-priority badge-{{ $task->priority }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-status badge-{{ $task->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td>
                                {{ $task->deadline ? $task->deadline->format('d M Y') : '-' }}
                            </td>
                            <td class="text-right">
                                <a href="{{ route('tasks.edit', [$project, $task]) }}" class="btn btn-sm btn-secondary">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('tasks.destroy', [$project, $task]) }}" 
                                    class="d-inline" 
                                    onsubmit="return confirm('Hapus task ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="empty-state">
        <p class="empty-state-text">Belum ada task di project ini.</p>
        <a href="{{ route('tasks.create', $project) }}" class="btn btn-primary btn-sm">
            + Buat Task Pertama
        </a>
    </div>
@endif