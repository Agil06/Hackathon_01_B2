<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id',
        'name',
    ];

    protected $appends = [
        'progress',
    ];

    /**
     * The user who created the project.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Members/collaborators belonging to this project.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withTimestamps();
    }

    /**
     * Tasks belonging to this project.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Determine whether the given user is a member of this project.
     */
    public function hasMember(User $user): bool
    {
        if ($this->relationLoaded('members')) {
            return $this->members->contains('id', $user->id);
        }

        return $this->members()->where('users.id', $user->id)->exists();
    }

    /**
     * Computed progress:
     * - 'Not Started' if 0 tasks or all tasks are 'not_done'
     * - 'Completed' if tasks exist and all tasks are 'done'
     * - 'In Progress' otherwise
     */
    public function getProgressAttribute(): string
    {
        $tasks = $this->relationLoaded('tasks') ? $this->tasks : $this->tasks()->get();
        $total = $tasks->count();

        if ($total === 0) {
            return 'Not Started';
        }

        $doneCount = $tasks->where('status', 'done')->count();
        if ($doneCount === $total) {
            return 'Completed';
        }

        $notDoneCount = $tasks->where('status', 'not_done')->count();
        if ($notDoneCount === $total) {
            return 'Not Started';
        }

        return 'In Progress';
    }
}
