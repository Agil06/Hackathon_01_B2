<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

test('guest cannot access project index and is redirected to login', function () {
    $response = $this->get(route('projects.index'));

    $response->assertRedirect(route('login'));
});

test('user can see project index with empty state when having no projects', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get(route('projects.index'));

    $response->assertOk();
    $response->assertSee('No projects yet');
});

test('user can only see projects they are a member of (FR-07)', function () {
    $user1 = User::factory()->create(['role' => 'user']);
    $user2 = User::factory()->create(['role' => 'user']);

    $project1 = Project::create(['name' => 'Alpha Project', 'creator_id' => $user1->id]);
    $project1->members()->attach($user1->id);

    $project2 = Project::create(['name' => 'Beta Project', 'creator_id' => $user2->id]);
    $project2->members()->attach($user2->id);

    $response = $this->actingAs($user1)->get(route('projects.index'));

    $response->assertOk();
    $response->assertSee('Alpha Project');
    $response->assertDontSee('Beta Project');
});

test('user can create a project and automatically becomes a member (FR-08, AC-08)', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => 'New Awesome Project',
    ]);

    $project = Project::where('name', 'New Awesome Project')->first();

    expect($project)->not->toBeNull()
        ->and($project->creator_id)->toBe($user->id)
        ->and($project->members()->where('users.id', $user->id)->exists())->toBeTrue();

    $response->assertRedirect(route('projects.show', $project));
});

test('project creation validation requires a name and max 255 chars (BR-06)', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => '',
    ]);
    $response->assertSessionHasErrors(['name']);

    $responseLong = $this->actingAs($user)->post(route('projects.store'), [
        'name' => str_repeat('a', 256),
    ]);
    $responseLong->assertSessionHasErrors(['name']);
});

test('project member can view project details (FR-09, AC-09)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Detail Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $response = $this->actingAs($user)->get(route('projects.show', $project));

    $response->assertOk();
    $response->assertSee('Detail Project');
    $response->assertSee($user->name);
    $response->assertSee('Not Started');
});

test('non-member cannot view project details and receives 403 (FR-13, AC-10)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $stranger = User::factory()->create(['role' => 'user']);

    $project = Project::create(['name' => 'Secret Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $response = $this->actingAs($stranger)->get(route('projects.show', $project));

    $response->assertForbidden();
});

test('member can update project name (FR-10, AC-11)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Old Name', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $response = $this->actingAs($user)->patch(route('projects.update', $project), [
        'name' => 'Updated Project Name',
    ]);

    $response->assertRedirect(route('projects.show', $project));
    expect($project->fresh()->name)->toBe('Updated Project Name');
});

test('non-member cannot update project name and receives 403 (FR-13, AC-10)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $stranger = User::factory()->create(['role' => 'user']);

    $project = Project::create(['name' => 'Original Name', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $response = $this->actingAs($stranger)->patch(route('projects.update', $project), [
        'name' => 'Malicious Rename',
    ]);

    $response->assertForbidden();
    expect($project->fresh()->name)->toBe('Original Name');
});

test('project update validation rejects empty name (AC-11)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Original Name', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $response = $this->actingAs($user)->patch(route('projects.update', $project), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors(['name']);
    expect($project->fresh()->name)->toBe('Original Name');
});

test('member can permanently delete project with cascading tasks and memberships (FR-11, FR-24, AC-12)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Project To Delete', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    $task = Task::create([
        'project_id' => $project->id,
        'title' => 'Cascade Task',
        'priority' => 'medium',
        'status' => 'not_done',
    ]);

    $response = $this->actingAs($user)->delete(route('projects.destroy', $project));

    $response->assertRedirect(route('projects.index'));

    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    $this->assertDatabaseMissing('project_user', ['project_id' => $project->id]);
});

test('non-member cannot delete project and receives 403 (FR-13, AC-10)', function () {
    $owner = User::factory()->create(['role' => 'user']);
    $stranger = User::factory()->create(['role' => 'user']);

    $project = Project::create(['name' => 'Protected Project', 'creator_id' => $owner->id]);
    $project->members()->attach($owner->id);

    $response = $this->actingAs($stranger)->delete(route('projects.destroy', $project));

    $response->assertForbidden();
    $this->assertDatabaseHas('projects', ['id' => $project->id]);
});

test('computed progress handles all conditions correctly (FR-21, FR-22, FR-23, AC-18, AC-19, AC-20)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $project = Project::create(['name' => 'Progress Test Project', 'creator_id' => $user->id]);
    $project->members()->attach($user->id);

    // Condition 1: 0 tasks -> 'Not Started'
    expect($project->progress)->toBe('Not Started');

    // Condition 2: all tasks 'not_done' -> 'Not Started'
    $task1 = Task::create([
        'project_id' => $project->id,
        'title' => 'Task 1',
        'priority' => 'low',
        'status' => 'not_done',
    ]);
    $task2 = Task::create([
        'project_id' => $project->id,
        'title' => 'Task 2',
        'priority' => 'medium',
        'status' => 'not_done',
    ]);

    $project->unsetRelation('tasks');
    expect($project->progress)->toBe('Not Started');

    // Condition 3: at least 1 task 'in_progress' -> 'In Progress'
    $task1->update(['status' => 'in_progress']);
    $project->unsetRelation('tasks');
    expect($project->progress)->toBe('In Progress');

    // Condition 4: 1 done, 1 not_done -> 'In Progress'
    $task1->update(['status' => 'done']);
    $project->unsetRelation('tasks');
    expect($project->progress)->toBe('In Progress');

    // Condition 5: all tasks 'done' -> 'Completed'
    $task2->update(['status' => 'done']);
    $project->unsetRelation('tasks');
    expect($project->progress)->toBe('Completed');

    // Condition 6: revert one task to 'in_progress' -> back to 'In Progress'
    $task2->update(['status' => 'in_progress']);
    $project->unsetRelation('tasks');
    expect($project->progress)->toBe('In Progress');
});
