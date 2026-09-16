<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Task update is restricted to project members (FR-12, BR-11, DoD).
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
     * FR-12, BR-03, BR-04, BR-05.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['not_done', 'in_progress', 'done'])],
            // Assignee hook for Abhi (FR-14, programmer.md)
            'assigned_user_ids' => ['nullable', 'array'],
            'assigned_user_ids.*' => ['integer'],
        ];
    }
}
