<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagId = $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'unique:tags,name,'.$tagId,
            ],
        ];
    }
}
