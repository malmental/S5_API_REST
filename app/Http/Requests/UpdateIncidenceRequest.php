<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:open,in_progress,closed'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'tags' => ['nullable', 'string'],
        ];
    }
}
