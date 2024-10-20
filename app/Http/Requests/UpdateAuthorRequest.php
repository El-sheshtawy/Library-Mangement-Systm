<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|unique:authors,name,' . $this->route('author')->id . '|max:255',
            'biography' => 'sometimes|nullable|string',
            'birthdate' => 'sometimes|nullable|date',
            'profile_image' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:2048',
        ];
    }
}
