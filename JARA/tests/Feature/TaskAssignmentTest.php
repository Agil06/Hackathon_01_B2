<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

function assignmentProjectWithMembers(): array
{
    $owner = User::factory()->create(['role' => 'user']);
    $member = User::factory()->create(['role' => 'user']);
    $outsider = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Assignment Project', 'creator_id' => $owner->id]);
    $project->members()->attach([$owner->id, $member->id]);
    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Coordinate release',
        'priority' => 'high',
        'status' => 'not_done',
    ]);

    return compact('owner', 'member', 'outsider', 'project', 'task');
}

test('member can assign multiple project members to a task', function () {
    ['owner' => $owner, 'member' => $member, 'project' => $project, 'task' => $task] = assignmentProjectWithMembers();

    $this->actingAs($owner)
        ->put(route('tasks.assignees.update', [$project, $task]), [
            'assignee_ids' => [$owner->id, $member->id],
        ])
        ->assertRedirect(route('tasks.show', [$project, $task]));

    $this->assertDatabaseHas('task_user', ['task_id' => $task->id, 'user_id' => $owner->id]);
    $this->assertDatabaseHas('task_user', ['task_id' => $task->id, 'user_id' => $member->id]);
});

test('assignment rejects a user who is not a project member without changing existing assignments', function () {
    ['owner' => $owner, 'member' => $member, 'outsider' => $outsider, 'project' => $project, 'task' => $task] = assignmentProjectWithMembers();
    $task->assignees()->attach($member->id);

    $this->actingAs($owner)
        ->from(route('tasks.assignees.edit', [$project, $task]))
        ->put(route('tasks.assignees.update', [$project, $task]), [
            'assignee_ids' => [$member->id, $outsider->id],
        ])
        ->assertRedirect(route('tasks.assignees.edit', [$project, $task]))
        ->assertSessionHasErrors('assignee_ids');

    expect($task->fresh()->assignees()->pluck('users.id')->all())->toBe([$member->id]);
});

test('tasks mine only shows tasks assigned to the current user', function () {
    ['owner' => $owner, 'member' => $member, 'project' => $project, 'task' => $task] = assignmentProjectWithMembers();
    $task->assignees()->attach($member->id);

    $otherTask = Task::create([
        'project_id' => $project->id,
        'title' => 'Owner only task',
        'priority' => 'medium',
        'status' => 'not_done',
    ]);
    $otherTask->assignees()->attach($owner->id);

    $this->actingAs($member)
        ->get(route('tasks.mine'))
        ->assertOk()
        ->assertSee('Coordinate release')
        ->assertDontSee('Owner only task');
});

test('non-member cannot manage task assignees', function () {
    ['outsider' => $outsider, 'project' => $project, 'task' => $task] = assignmentProjectWithMembers();

    $this->actingAs($outsider)
        ->get(route('tasks.assignees.edit', [$project, $task]))
        ->assertForbidden();

    $this->actingAs($outsider)
        ->put(route('tasks.assignees.update', [$project, $task]), ['assignee_ids' => []])
        ->assertForbidden();
});
