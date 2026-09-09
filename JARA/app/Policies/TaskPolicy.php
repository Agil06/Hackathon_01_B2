<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if user can view/create/update/delete task.
     * Task access is checked through project membership.
     */
    public function manage(User $user, Project $project): bool
    {
        // User must be a member of the project
        return $project->members()->where('user_id', $user->id)->exists();
    }
}