<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicationRequest extends FormRequest
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
            'publish' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected', 
            'copyright_image' => 'nullable|image',
            'user_id' => 'required|exists:users,id', 
            'book_id' => 'required|exists:books,id', 
        ];
    }
}
