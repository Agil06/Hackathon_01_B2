<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * Display a listing of the user's projects. (FR-07)
     */
    public function index(Request $request)
    {
        $projects = $request->user()
            ->projects()
            ->with(['creator', 'tasks', 'members'])
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project. (FR-08)
     */
    public function create()
    {
        Gate::authorize('create', Project::class);

        return view('projects.create');
    }

    /**
     * Store a newly created project in storage. (FR-08)
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Project::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $project = DB::transaction(function () use ($validated, $request) {
            $project = Project::create([
                'name' => $validated['name'],
                'creator_id' => $request->user()->id,
            ]);

            $project->members()->attach($request->user()->id);

            return $project;
        });

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project. (FR-09)
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        $project->load(['creator', 'members', 'tasks']);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project. (FR-10)
     */
    public function edit(Project $project)
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified project in storage. (FR-10)
     */
    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $project->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project from storage. (FR-11)
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
