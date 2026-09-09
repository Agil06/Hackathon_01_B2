<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CollaboratorController extends Controller
{
    /**
     * Add an existing registered user as a collaborator to the project.
     *
     * FR-12: Anggota project dapat menambahkan akun terdaftar sebagai collaborator berdasarkan email.
     * FR-13: Sistem menolak akses melihat atau memanipulasi project bagi non-anggota.
     * BR-08: Membership (project_id, user_id) unik; duplikasi ditolak.
     * BR-09: Ditambahkan langsung tanpa invitation atau approval.
     * BR-10: Membutuhkan authentication dan membership project.
     *
     * @throws ValidationException
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('view', $project);

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

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

        return redirect()->route('projects.show', $project)
            ->with('success', "Collaborator '{$userToAdd->name}' added successfully.");
    }
}
