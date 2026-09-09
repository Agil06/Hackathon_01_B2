<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('guest cannot add collaborator and is redirected to login', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Secret Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $response = $this->post(route('collaborators.store', $project), [
        'email' => 'collaborator@example.com',
    ]);

    $response->assertRedirect(route('login'));
});

test('non-member cannot add collaborator and receives 403 (FR-13, AC-10)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $stranger = User::factory()->create(['role' => 'user']);
    $targetUser = User::factory()->create(['role' => 'user']);

    $project = Project::create(['name' => 'Owner Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $response = $this->actingAs($stranger)->post(route('collaborators.store', $project), [
        'email' => $targetUser->email,
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('project_user', [
        'project_id' => $project->id,
        'user_id' => $targetUser->id,
    ]);
});

test('project member can add registered user as collaborator directly without invitation (FR-12, AC-13, BR-09)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $collaborator = User::factory()->create(['name' => 'Jane Collab', 'role' => 'user', 'email' => 'jane@example.com']);

    $project = Project::create(['name' => 'Collab Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $response = $this->actingAs($owner)->post(route('collaborators.store', $project), [
        'email' => 'jane@example.com',
    ]);

    $response->assertRedirect(route('projects.show', $project));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $collaborator->id,
    ]);
    expect($project->hasMember($collaborator))->toBeTrue();
});

test('adding collaborator rejects unregistered email with validation error (AC-13, BR-09)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Collab Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $response = $this->actingAs($owner)->post(route('collaborators.store', $project), [
        'email' => 'unregistered@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertDatabaseCount('project_user', 1);
});

test('adding collaborator rejects duplicate membership with validation error (AC-13, BR-08)', function () {
    $owner = User::factory()->create(['role' => 'user', 'email' => 'owner@example.com']);
    $collaborator = User::factory()->create(['role' => 'user', 'email' => 'existing@example.com']);

    $project = Project::create(['name' => 'Collab Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);
    $project->members()->attach($collaborator->id);

    // Re-adding the creator
    $responseCreator = $this->actingAs($owner)->post(route('collaborators.store', $project), [
        'email' => 'owner@example.com',
    ]);
    $responseCreator->assertSessionHasErrors(['email']);

    // Re-adding the existing collaborator
    $responseCollab = $this->actingAs($owner)->post(route('collaborators.store', $project), [
        'email' => 'existing@example.com',
    ]);
    $responseCollab->assertSessionHasErrors(['email']);

    $this->assertDatabaseCount('project_user', 2);
});

test('collaborator immediately sees the project in project list (DoD, FR-07)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $collaborator = User::factory()->create(['name' => 'Alice Member', 'role' => 'user']);

    $project = Project::create(['name' => 'Team Collaboration Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    // Before being added
    $responseBefore = $this->actingAs($collaborator)->get(route('projects.index'));
    $responseBefore->assertOk();
    $responseBefore->assertDontSee('Team Collaboration Project');

    // Add collaborator
    $this->actingAs($owner)->post(route('collaborators.store', $project), [
        'email' => $collaborator->email,
    ]);

    // Immediately visible in index
    $responseAfter = $this->actingAs($collaborator)->get(route('projects.index'));
    $responseAfter->assertOk();
    $responseAfter->assertSee('Team Collaboration Project');
});

test('collaborator has equal access to view, update, and delete the project (DoD)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $collaborator = User::factory()->create(['name' => 'Bob Collab', 'role' => 'user']);

    $project = Project::create(['name' => 'Shared Project', 'creator_id' => $owner->id]);
    $project->members()->attach([$owner->id, $collaborator->id]);

    // Collaborator can view project details
    $this->actingAs($collaborator)->get(route('projects.show', $project))->assertOk()->assertSee('Shared Project');

    // Collaborator can update project
    $this->actingAs($collaborator)->patch(route('projects.update', $project), [
        'name' => 'Updated by Bob',
    ])->assertRedirect(route('projects.show', $project));
    expect($project->fresh()->name)->toBe('Updated by Bob');

    // Collaborator can delete project
    $this->actingAs($collaborator)->delete(route('projects.destroy', $project))->assertRedirect(route('projects.index'));
    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

test('collaborator has equal access to create, view, update, and delete tasks in project (DoD)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $collaborator = User::factory()->create(['role' => 'user']);

    $project = Project::create(['name' => 'Task Collab Project', 'creator_id' => $owner->id]);
    $project->members()->attach([$owner->id, $collaborator->id]);

    // Collaborator creates a task
    $createTaskResponse = $this->actingAs($collaborator)->post(route('tasks.store', $project), [
        'title' => 'Collab Task',
        'priority' => 'high',
        'deadline' => now()->addDays(3)->format('Y-m-d'),
    ]);
    $createTaskResponse->assertRedirect(route('projects.show', $project));

    $task = Task::where('title', 'Collab Task')->first();
    expect($task)->not->toBeNull()
        ->and($task->project_id)->toBe($project->id)
        ->and($task->priority)->toBe('high')
        ->and($task->status)->toBe('not_done');

    // Collaborator views the task
    $this->actingAs($collaborator)->get(route('tasks.show', [$project, $task]))->assertOk()->assertSee('Collab Task');

    // Collaborator updates task status to in_progress
    $this->actingAs($collaborator)->patch(route('tasks.update', [$project, $task]), [
        'title' => 'Collab Task Updated',
        'priority' => 'high',
        'status' => 'in_progress',
    ])->assertRedirect(route('projects.show', $project));
    expect($task->fresh()->status)->toBe('in_progress');

    // Collaborator deletes task
    $this->actingAs($collaborator)->delete(route('tasks.destroy', [$project, $task]))->assertRedirect(route('projects.show', $project));
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('collaborator can add another collaborator to the project (equality of access, FR-12)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $collab1 = User::factory()->create(['role' => 'user']);
    $collab2 = User::factory()->create(['name' => 'Third Member', 'role' => 'user', 'email' => 'third@example.com']);

    $project = Project::create(['name' => 'Chain Project', 'creator_id' => $owner->id]);
    $project->members()->attach([$owner->id, $collab1->id]);

    // Collab1 adds Collab2
    $response = $this->actingAs($collab1)->post(route('collaborators.store', $project), [
        'email' => 'third@example.com',
    ]);

    $response->assertRedirect(route('projects.show', $project));
    expect($project->hasMember($collab2))->toBeTrue();
});

test('collaborator form is displayed on project detail page for members', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Form View Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $response = $this->actingAs($owner)->get(route('projects.show', $project));

    $response->assertOk();
    $response->assertSee('Add Collaborator');
    $response->assertSee('Collaborator Email');
    $response->assertSee(route('collaborators.store', $project));
});

test('non-member cannot view or manipulate project or tasks (DoD, FR-13, AC-10)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $nonMember = User::factory()->create(['role' => 'user']);

    $project = Project::create(['name' => 'Protected Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Secret Task',
        'priority' => 'low',
        'status' => 'not_done',
    ]);

    // Non-member cannot show project
    $this->actingAs($nonMember)->get(route('projects.show', $project))->assertForbidden();

    // Non-member cannot edit project
    $this->actingAs($nonMember)->get(route('projects.edit', $project))->assertForbidden();

    // Non-member cannot update project
    $this->actingAs($nonMember)->patch(route('projects.update', $project), ['name' => 'Hacked'])->assertForbidden();

    // Non-member cannot delete project
    $this->actingAs($nonMember)->delete(route('projects.destroy', $project))->assertForbidden();

    // Non-member cannot view task
    $this->actingAs($nonMember)->get(route('tasks.show', [$project, $task]))->assertForbidden();

    // Non-member cannot edit task
    $this->actingAs($nonMember)->get(route('tasks.edit', [$project, $task]))->assertForbidden();

    // Non-member cannot create task
    $this->actingAs($nonMember)->post(route('tasks.store', $project), ['title' => 'Hack Task', 'priority' => 'low'])->assertForbidden();

    // Non-member cannot update task
    $this->actingAs($nonMember)->patch(route('tasks.update', [$project, $task]), ['title' => 'Hack', 'priority' => 'low', 'status' => 'done'])->assertForbidden();

    // Non-member cannot delete task
    $this->actingAs($nonMember)->delete(route('tasks.destroy', [$project, $task]))->assertForbidden();

    // Non-member cannot add collaborator
    $target = User::factory()->create(['role' => 'user']);
    $this->actingAs($nonMember)->post(route('collaborators.store', $project), ['email' => $target->email])->assertForbidden();
});
