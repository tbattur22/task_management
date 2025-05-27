<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TaskFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Returns 422 response for Inertia to notify the validation errors
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    protected function failedValidation(Validator $validator): void
    {
        $projectId = (int) $this->input('project_id');
        $project = Project::findOrFail($projectId);

        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $taskId = $isUpdate
            ? (is_object($this->route('task')) ? $this->route('task')->id : $this->route('task'))
            : null;
        if ($taskId) {
            $task = Task::findOrFail($taskId);
        }

        // Return proper 422 response for Inertia
        throw new HttpResponseException(
            Inertia::render('tasks/create_edit', [
                'project' => $project,
                'taskToEdit' => $task ?? null,
                'errors' => $validator->errors(),
                'values'=> $this->all(),
            ])->toResponse($this)->setStatusCode(422)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        // if it is update need to get the task id
        $taskId = $isUpdate
            ? (is_object($this->route('task')) ? $this->route('task')->id : $this->route('task'))
            : null;

        // tasks table has composite unique index on project_id and priority columns
        $uniquePriorityRule = Rule::unique('tasks')
            ->where(function ($query) {
                return $query->where('project_id', (int) $this->input('project_id'));
            });

        // if it is update need to ignore the current record for enforcing the unique index
        if ($isUpdate && $taskId) {
            $uniquePriorityRule->ignore($taskId);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['bail','required', 'exists:projects,id'],
            'priority' => [
                'required',
                'integer',
                $uniquePriorityRule,
            ],
            'created_at' => ['nullable','date'],
            'updated_at'=> ['nullable','date'],
        ];
    }
}
