<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Project;
use Inertia\Inertia;

class ProjectFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name"=> ["required","string","min:3"],
            'created_at' => ['nullable','date'],
            'updated_at'=> ['nullable','date'],
        ];
    }

    /**
     * Returns 422 response for Inertia to notify the validation errors
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    protected function failedValidation(Validator $validator): void
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        $projectId = $isUpdate
            ? $this->route('id')
            : null;
        if ($projectId) {
            $project = Project::findOrFail($projectId);
        }

        // Return proper 422 response for Inertia
        throw new HttpResponseException(
            Inertia::render('projects/create_edit', [
                'projectToEdit' => $project ?? null,
                'errors' => $validator->errors(),
                'values'=> $this->all(),
            ])->toResponse($this)->setStatusCode(422)
        );
    }
}
