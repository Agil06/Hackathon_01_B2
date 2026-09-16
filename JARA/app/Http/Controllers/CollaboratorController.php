<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CollaboratorController extends Controller
{
    /**
     * Add an existing registered user as a collaborator to the project.
     *
     * FR-08: Owner dapat menambahkan akun terdaftar sebagai member berdasarkan email.
     * FR-09: Sistem menolak akses melihat atau memanipulasi project bagi non-anggota.
     * BR-07: Membership (project_id, user_id) unik; duplikasi ditolak.
     * BR-08: Hanya owner yang dapat mengelola membership.
     *
     * @throws ValidationException
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('manageMembers', $project);

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $userToAdd = DB::transaction(function () use ($project, $validated): User {
            $userToAdd = User::where('email', $validated['email'])->first();

            if (! $userToAdd) {
                throw ValidationException::withMessages([
                    'email' => ['The specified email is not registered.'],
                ]);
            }

            if ($project->hasMember($userToAdd)) {
                throw ValidationException::withMessages([
                    'email' => ['This user is already a member of this project.'],
                ]);
            }

            $project->members()->attach($userToAdd->id);

            return $userToAdd;
        });

        return redirect()->route('projects.show', $project)
            ->with('success', "Collaborator '{$userToAdd->name}' added successfully.");
    }

    public function destroy(Project $project, User $user): RedirectResponse
    {
        Gate::authorize('manageMembers', $project);

        DB::transaction(function () use ($project, $user): void {
            if ($user->id === $project->owner_id) {
                throw ValidationException::withMessages([
                    'email' => ['The project owner cannot be removed.'],
                ]);
            }

            if (! $project->members()->whereKey($user->id)->exists()) {
                throw ValidationException::withMessages([
                    'email' => ['This user is not a member of the project.'],
                ]);
            }

            $taskIds = $project->tasks()->pluck('tasks.id');
            DB::table('task_user')
                ->whereIn('task_id', $taskIds)
                ->where('user_id', $user->id)
                ->delete();

            $project->members()->detach($user->id);
        });

        return redirect()->route('projects.show', $project)
            ->with('success', "Collaborator '{$user->name}' removed successfully.");
    }
}
