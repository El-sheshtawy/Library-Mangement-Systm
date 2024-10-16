<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'name' => $this->name,
            'biography' => $this->biography,
            'birthdate' => $this->birthdate,
            'image' => $this->hasMedia('author_requests') ? $this->getFirstMediaUrl('author_requests') : $this->getFirstMediaUrl('author'), 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            // 'books' => $this->books->map(function ($book) {
            //     return [
            //         'id' => $book->id,
            //         'title' => $book->title,
            //         'image_url' => $book->getFirstMediaUrl('images'), 
            //     ];
            // }),
        ];
    }
}
