<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskAssignmentController extends Controller
{
    public function edit(Project $project, Task $task)
    {
        $this->authorize('view', $project);
        $this->ensureTaskBelongsToProject($project, $task);

        $members = $project->members()->orderBy('name')->get();
        $task->load('assignees');

        return view('tasks.assignees.edit', compact('project', 'task', 'members'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('view', $project);
        $this->ensureTaskBelongsToProject($project, $task);

        $validated = $request->validate([
            'assignee_ids' => ['nullable', 'array'],
            'assignee_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $assigneeIds = collect($validated['assignee_ids'] ?? [])
            ->map(static fn ($id): int => (int) $id)
            ->values();

        DB::transaction(function () use ($project, $task, $assigneeIds): void {
            $memberCount = $project->members()
                ->whereKey($assigneeIds)
                ->count();

            if ($memberCount !== $assigneeIds->count()) {
                throw ValidationException::withMessages([
                    'assignee_ids' => 'Semua assignee harus merupakan anggota project ini.',
                ]);
            }

            $task->assignees()->sync($assigneeIds->all());
        });

        return redirect()->route('tasks.show', [$project, $task])
            ->with('success', 'Assignee task berhasil diperbarui.');
    }

    public function mine(Request $request)
    {
        $tasks = $request->user()
            ->assignedTasks()
            ->with(['project', 'assignees'])
            ->latest('tasks.updated_at')
            ->get();

        return view('tasks.mine', compact('tasks'));
    }

    private function ensureTaskBelongsToProject(Project $project, Task $task): void
    {
        if ($task->project_id !== $project->id) {
            abort(404);
        }
    }
}
