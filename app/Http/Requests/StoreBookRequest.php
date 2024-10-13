<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|mimes:pdf,doc,docx',  
            'cover_image' => 'required|image',  
            'size' => 'nullable|numeric',
            'number_pages' => 'nullable|integer',
            'published_at' => 'nullable|date',
            'is_approved' => 'boolean',
            'views_count' => 'integer|min:0',
            'downloads_count' => 'integer|min:0',
            'lang' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'author_id' => 'required|exists:authors,id',
            'book_series_id' => 'nullable|exists:book_series,id',
        ];
    }
}
