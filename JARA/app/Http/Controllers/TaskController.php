<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Show task creation form.
     * FR-14: Anggota project dapat membuat task di dalam project
     */
    public function create(Project $project)
    {
        // Authorization: hanya member project yang boleh
        $this->authorize('view', $project);

        $priorities = ['low', 'medium', 'high'];

        return view('tasks.create', compact('project', 'priorities'));
    }

    /**
     * Store a new task.
     * FR-14: Membuat task
     * FR-18: Task baru memiliki status default 'Not Done'
     * FR-19: Priority dibatasi
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'deadline' => ['nullable', 'date'],
        ]);

        $task = $project->tasks()->create([
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'deadline' => $validated['deadline'] ?? null,
            'status' => 'not_done', // FR-18: Status default
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', "Task '{$task->title}' berhasil dibuat.");
    }

    /**
     * Show task detail.
     * FR-15: Anggota project dapat melihat detail task
     */
    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $project);

        // Pastikan task milik project yang diminta
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        return view('tasks.show', compact('project', 'task'));
    }

    /**
     * Show task edit form.
     * FR-16: Anggota project dapat mengubah task
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
     * FR-16: Mengubah title, priority, deadline, status
     * FR-20: Status boleh berpindah antarnilai tanpa approval
     */
    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('view', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['not_done', 'in_progress', 'done'])],
        ]);

        $task->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', "Task '{$task->title}' berhasil diperbarui.");
    }

    /**
     * Delete task.
     * FR-17: Anggota project dapat menghapus permanen task
     * BR-17: Hard delete
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