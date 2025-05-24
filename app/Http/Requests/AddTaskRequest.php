<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTaskRequest extends FormRequest
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
            'tasks' => 'required|array',
            'tasks.*.name' => 'required|string',
            'tasks.*.duration' => 'required|integer',
            'tasks.*.successors' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'tasks.required' => 'La liste des tâches est requise.',
            'tasks.array' => 'Les tâches doivent être fournies sous forme de tableau.',
            'tasks.*.name.required' => 'Le nom de chaque tâche est requis.',
            'tasks.*.name.string' => 'Le nom de chaque tâche doit être une chaîne de caractères.',
            'tasks.*.duration.required' => 'La durée de chaque tâche est requise.',
            'tasks.*.duration.integer' => 'La durée de chaque tâche doit être un entier.',
            'tasks.*.successors.array' => 'Les successeurs doivent être un tableau.',
        ];
    }
}
