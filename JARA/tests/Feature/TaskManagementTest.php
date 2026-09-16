<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('guest cannot access task routes and is redirected to login', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Secret Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Sample Task',
        'priority' => 'medium',
        'status' => 'not_done',
    ]);

    $this->get(route('tasks.create', $project))->assertRedirect(route('login'));
    $this->post(route('tasks.store', $project), ['title' => 'New Task', 'priority' => 'medium'])->assertRedirect(route('login'));
    $this->get(route('tasks.show', [$project, $task]))->assertRedirect(route('login'));
    $this->get(route('tasks.edit', [$project, $task]))->assertRedirect(route('login'));
    $this->patch(route('tasks.update', [$project, $task]), ['title' => 'Updated', 'priority' => 'low', 'status' => 'done'])->assertRedirect(route('login'));
    $this->delete(route('tasks.destroy', [$project, $task]))->assertRedirect(route('login'));
});

test('member can view task creation form (FR-14)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Demo Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $response = $this->actingAs($user)->get(route('tasks.create', $project));

    $response->assertOk();
    $response->assertSee('Buat Task Baru');
    $response->assertSee($project->name);
});

test('member can create task with default not_done status (FR-14, FR-18, AC-14)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Demo Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $response = $this->actingAs($user)->post(route('tasks.store', $project), [
        'title' => 'Implement Auth System',
        'priority' => 'high',
        'deadline' => now()->addDays(5)->format('Y-m-d'),
    ]);

    $response->assertRedirect(route('projects.show', $project));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', [
        'project_id' => $project->id,
        'title' => 'Implement Auth System',
        'priority' => 'high',
        'status' => 'not_done', // FR-18: Status default Not Done
    ]);
});

test('task creation validates title, priority, and optional deadline (BR-06, BR-12, BR-13, AC-16)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Demo Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    // Title kosong
    $this->actingAs($user)->post(route('tasks.store', $project), [
        'title' => '',
        'priority' => 'medium',
    ])->assertSessionHasErrors(['title']);

    // Title melebihi 255 karakter
    $this->actingAs($user)->post(route('tasks.store', $project), [
        'title' => str_repeat('a', 256),
        'priority' => 'medium',
    ])->assertSessionHasErrors(['title']);

    // Priority di luar enum (low, medium, high)
    $this->actingAs($user)->post(route('tasks.store', $project), [
        'title' => 'Task with invalid priority',
        'priority' => 'urgent',
    ])->assertSessionHasErrors(['priority']);

    // Deadline bukan tanggal valid
    $this->actingAs($user)->post(route('tasks.store', $project), [
        'title' => 'Task with invalid deadline',
        'priority' => 'low',
        'deadline' => 'not-a-valid-date',
    ])->assertSessionHasErrors(['deadline']);

    // Deadline opsional (boleh null)
    $this->actingAs($user)->post(route('tasks.store', $project), [
        'title' => 'Task without deadline',
        'priority' => 'low',
        'deadline' => null,
    ])->assertRedirect(route('projects.show', $project));

    $this->assertDatabaseHas('tasks', [
        'project_id' => $project->id,
        'title' => 'Task without deadline',
        'deadline' => null,
    ]);
});

test('member can view task details (FR-15, AC-09)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Demo Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Design Wireframes',
        'priority' => 'medium',
        'status' => 'not_done',
        'deadline' => '2026-10-01',
    ]);

    $response = $this->actingAs($user)->get(route('tasks.show', [$project, $task]));

    $response->assertOk();
    $response->assertSee('Design Wireframes');
    $response->assertSee('Medium');
    $response->assertSee('Not done');
});

test('member can view task edit form (FR-16)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Demo Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Task to edit',
        'priority' => 'low',
        'status' => 'not_done',
    ]);

    $response = $this->actingAs($user)->get(route('tasks.edit', [$project, $task]));

    $response->assertOk();
    $response->assertSee('Edit Task');
    $response->assertSee('Task to edit');
});

test('member can update task title, priority, deadline, and status (FR-16, FR-20, AC-15)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Demo Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Original Task Title',
        'priority' => 'low',
        'status' => 'not_done',
        'deadline' => '2026-10-01',
    ]);

    $response = $this->actingAs($user)->patch(route('tasks.update', [$project, $task]), [
        'title' => 'Updated Task Title',
        'priority' => 'high',
        'status' => 'in_progress',
        'deadline' => '2026-10-15',
    ]);

    $response->assertRedirect(route('projects.show', $project));
    $response->assertSessionHas('success');

    $freshTask = $task->fresh();
    expect($freshTask->title)->toBe('Updated Task Title')
        ->and($freshTask->priority)->toBe('high')
        ->and($freshTask->status)->toBe('in_progress')
        ->and($freshTask->deadline->format('Y-m-d'))->toBe('2026-10-15');
});

test('task update rejects invalid priority or status values (AC-16, BR-12, BR-14)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Demo Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Task to test validation',
        'priority' => 'medium',
        'status' => 'not_done',
    ]);

    // Invalid status
    $this->actingAs($user)->patch(route('tasks.update', [$project, $task]), [
        'title' => 'Task Valid Title',
        'priority' => 'medium',
        'status' => 'blocked', // not valid
    ])->assertSessionHasErrors(['status']);

    // Invalid priority
    $this->actingAs($user)->patch(route('tasks.update', [$project, $task]), [
        'title' => 'Task Valid Title',
        'priority' => 'critical', // not valid
        'status' => 'not_done',
    ])->assertSessionHasErrors(['priority']);

    expect($task->fresh()->status)->toBe('not_done');
});

test('all status transitions between not_done, in_progress, and done are permitted without approval (FR-20, AC-20)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Demo Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Transition Task',
        'priority' => 'medium',
        'status' => 'not_done',
    ]);

    // not_done -> in_progress
    $this->actingAs($user)->patch(route('tasks.update', [$project, $task]), [
        'title' => 'Transition Task',
        'priority' => 'medium',
        'status' => 'in_progress',
    ]);
    expect($task->fresh()->status)->toBe('in_progress');

    // in_progress -> done
    $this->actingAs($user)->patch(route('tasks.update', [$project, $task]), [
        'title' => 'Transition Task',
        'priority' => 'medium',
        'status' => 'done',
    ]);
    expect($task->fresh()->status)->toBe('done');

    // done -> in_progress (revert)
    $this->actingAs($user)->patch(route('tasks.update', [$project, $task]), [
        'title' => 'Transition Task',
        'priority' => 'medium',
        'status' => 'in_progress',
    ]);
    expect($task->fresh()->status)->toBe('in_progress');

    // in_progress -> not_done (revert)
    $this->actingAs($user)->patch(route('tasks.update', [$project, $task]), [
        'title' => 'Transition Task',
        'priority' => 'medium',
        'status' => 'not_done',
    ]);
    expect($task->fresh()->status)->toBe('not_done');
});

test('member can permanently delete task (FR-17, AC-17, BR-17)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Demo Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Task to be deleted',
        'priority' => 'low',
        'status' => 'not_done',
    ]);

    $response = $this->actingAs($user)->delete(route('tasks.destroy', [$project, $task]));

    $response->assertRedirect(route('projects.show', $project));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('non-member cannot view or manipulate project tasks and receives 403 (FR-13, AC-10)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $stranger = User::factory()->create(['role' => 'user']);

    $project = Project::create(['name' => 'Owner Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Owner Task',
        'priority' => 'medium',
        'status' => 'not_done',
    ]);

    // Stranger cannot create task
    $this->actingAs($stranger)->get(route('tasks.create', $project))->assertForbidden();
    $this->actingAs($stranger)->post(route('tasks.store', $project), ['title' => 'Hacked', 'priority' => 'low'])->assertForbidden();

    // Stranger cannot view task
    $this->actingAs($stranger)->get(route('tasks.show', [$project, $task]))->assertForbidden();

    // Stranger cannot edit task
    $this->actingAs($stranger)->get(route('tasks.edit', [$project, $task]))->assertForbidden();

    // Stranger cannot update task
    $this->actingAs($stranger)->patch(route('tasks.update', [$project, $task]), ['title' => 'Hacked', 'priority' => 'low', 'status' => 'done'])->assertForbidden();

    // Stranger cannot delete task
    $this->actingAs($stranger)->delete(route('tasks.destroy', [$project, $task]))->assertForbidden();
});

test('accessing task belonging to another project returns 404 (mismatch protection)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project1 = Project::create(['name' => 'Project 1', 'creator_id' => $user->id]);
    $project2 = Project::create(['name' => 'Project 2', 'creator_id' => $user->id]);
    $project1->members()->attach($user->id);
    $project2->members()->attach($user->id);

    $taskInProject1 = Task::create([
        'project_id' => $project1->id,
        'title' => 'Task In Project 1',
        'priority' => 'medium',
        'status' => 'not_done',
    ]);

    // Accessing taskInProject1 using project2 URL should abort 404
    $this->actingAs($user)->get(route('tasks.show', [$project2, $taskInProject1]))->assertNotFound();
    $this->actingAs($user)->get(route('tasks.edit', [$project2, $taskInProject1]))->assertNotFound();
    $this->actingAs($user)->patch(route('tasks.update', [$project2, $taskInProject1]), [
        'title' => 'Mismatch update',
        'priority' => 'low',
        'status' => 'done',
    ])->assertNotFound();
    $this->actingAs($user)->delete(route('tasks.destroy', [$project2, $taskInProject1]))->assertNotFound();
});
