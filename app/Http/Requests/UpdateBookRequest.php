<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'file' => 'sometimes|required|mimes:pdf,doc,docx',  
            'cover_image' => 'sometimes|required|image', 
            'size' => 'sometimes|nullable|numeric',
            'number_pages' => 'sometimes|nullable|integer',
            'published_at' => 'sometimes|nullable|date',
            'is_approved' => 'sometimes|boolean',
            'views_count' => 'sometimes|integer|min:0',
            'downloads_count' => 'sometimes|integer|min:0',
            'lang' => 'sometimes|nullable|string|max:10',
            'category_id' => 'sometimes|required|exists:categories,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'author_id' => 'sometimes|required|exists:authors,id',
            'book_series_id' => 'sometimes|nullable|exists:book_series,id',
        ];
    }
}
