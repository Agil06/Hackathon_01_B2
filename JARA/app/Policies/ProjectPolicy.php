<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any projects.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the project.
     * Accessible only to project members (creator or collaborator).
     */
    public function view(User $user, Project $project): bool
    {
        return $project->hasMember($user);
    }

    /**
     * Determine whether the user can create projects.
     * Regular users can create projects; admin role is admin-only.
     */
    public function create(User $user): bool
    {
        return $user->role !== 'admin';
    }

    /**
     * Determine whether the user can update the project.
     * Accessible to all project members.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->hasMember($user);
    }

    /**
     * Determine whether the user can delete the project.
     * Accessible to all project members.
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->hasMember($user);
    }
}
