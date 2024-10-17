<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'biography' => $this->biography,
            'birthdate' => $this->birthdate,
            'books' => BookResource::collection($this->whenLoaded('books')),
            'media' => [
                'profile_image' => $this->getImageUrl('profile_image', 'medium'),
                'cover_image' => $this->getImageUrl('cover_image', 'large'),
                // Add other media URLs as needed
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
