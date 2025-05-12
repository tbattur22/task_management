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

    protected function failedValidation(Validator $validator): void
    {
        Log::error("failedValidation in TaskFormRequest");
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
            Inertia::render('task_create_edit', [
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
        Log::info("TaskFormRequest:rules()");
        DB::listen(function ($query) {
            Log::debug("SQL in rules(): {$query->sql}", $query->bindings);
        });

        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $taskId = $isUpdate
            ? (is_object($this->route('task')) ? $this->route('task')->id : $this->route('task'))
            : null;

        $uniquePriorityRule = Rule::unique('tasks')
            ->where(function ($query) {
                return $query->where('project_id', (int) $this->input('project_id'));
            });

        if ($isUpdate && $taskId) {
            $uniquePriorityRule->ignore($taskId);
        }

        if ($this->isMethod('post')) {
            Log::info("TaskFormRequest:rules(). Creating task. project id this->project_id: {$this->project_id}, this->input('project_id'): ".$this->input('project_id'));
        } elseif ($this->isMethod("put") || $this->isMethod("patch")) {
            Log::info("TaskFormRequest:rules(). Updating task. project id this->project_id: {$this->project_id}, this->input('project_id'): ".$this->input('project_id'));
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['bail','required', 'exists:projects,id'],
            'priority' => [
                'required',
                'integer',
                $uniquePriorityRule,
            ],
        ];
    }
}
