<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Task creation is restricted to project members (FR-10, BR-11, DoD).
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        if (is_numeric($project)) {
            $project = Project::find($project);
        }

        return $project instanceof Project && (bool) $this->user()?->can('view', $project);
    }

    /**
     * Get the validation rules that apply to the request.
     * FR-10, BR-03, BR-04.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'deadline' => ['nullable', 'date'],
            // Assignee hook for Abhi (FR-14, programmer.md)
            'assigned_user_ids' => ['nullable', 'array'],
            'assigned_user_ids.*' => ['integer'],
        ];
    }
}
