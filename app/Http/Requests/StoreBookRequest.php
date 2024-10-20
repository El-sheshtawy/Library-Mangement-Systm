<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization logic for creating a book
        return $this->user()->hasPermission('create-books');
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
            'published_at' => 'nullable|date',
            'is_approved' => 'required|boolean',
            'lang' => 'required|string|max:10',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:authors,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Cover image validation
            'file' => 'nullable|file|mimes:pdf|max:2048', // PDF file validation
        ];
    }
}
