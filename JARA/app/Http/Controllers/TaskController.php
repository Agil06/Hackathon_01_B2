<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Show task creation form.
     * FR-10: Anggota daftar dapat membuat tugas
     */
    public function create(Project $project)
    {
        // Authorization: hanya member daftar yang boleh (BR-11, DoD)
        $this->authorize('view', $project);

        $priorities = ['low', 'medium', 'high'];

        return view('tasks.create', compact('project', 'priorities'));
    }

    /**
     * Store a new task.
     * FR-10: Membuat tugas dengan judul, prioritas, tenggat waktu opsional
     * BR-04: Default priority 'medium'
     * BR-05 / FR-10: Status default 'not_done'
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('view', $project);

        $validated = $request->validated();

        $task = $project->tasks()->create([
            'title' => $validated['title'],
            'priority' => $validated['priority'] ?? 'medium',
            'deadline' => $validated['deadline'] ?? null,
            'status' => 'not_done', // FR-10: Status default not_done
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', "Task '{$task->title}' berhasil dibuat.");
    }

    /**
     * Show task detail.
     * FR-11: Anggota daftar dapat melihat detail dan daftar tugas
     */
    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        // Pastikan task milik daftar yang diminta (mismatch protection)
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        return view('tasks.show', compact('project', 'task'));
    }

    /**
     * Show task edit form.
     * FR-12: Anggota daftar dapat mengubah tugas
     */
    public function edit(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $priorities = ['low', 'medium', 'high'];
        $statuses = ['not_done', 'in_progress', 'done'];

        return view('tasks.edit', compact('project', 'task', 'priorities', 'statuses'));
    }

    /**
     * Update task.
     * FR-12: Mengubah judul, prioritas, tenggat waktu, dan status tugas
     * BR-05: Status perpindahan diizinkan antarnilai not_done, in_progress, done
     */
    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validated();

        $task->update([
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'deadline' => $validated['deadline'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', "Task '{$task->title}' berhasil diperbarui.");
    }

    /**
     * Mark task as done.
     * FR-12: Aksi menandai selesai mengubah status menjadi done
     * AC-11: Given task ditandai selesai, then statusnya done dan progres daftar diperbarui
     */
    public function markDone(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $task->update(['status' => 'done']);

        return redirect()->route('projects.show', $project)
            ->with('success', "Task '{$task->title}' berhasil ditandai selesai.");
    }

    /**
     * Alias for markDone.
     */
    public function complete(Project $project, Task $task)
    {
        return $this->markDone($project, $task);
    }

    /**
     * Delete task.
     * FR-13: Anggota daftar dapat menghapus tugas
     * BR-14: Hard delete; FK cascade menghapus assignment
     */
    public function destroy(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $taskTitle = $task->title;
        $task->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', "Task '{$taskTitle}' berhasil dihapus.");
    }
}
