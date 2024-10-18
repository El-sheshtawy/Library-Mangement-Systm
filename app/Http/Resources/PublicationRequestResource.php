<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicationRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'publisher_name' => $this->publisher_name,
            'status' => $this->status,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'book' => [
                'id' => $this->book->id,
                'title' => $this->book->title,
            ],
            'rejection_reason' => $this->rejection_reason,
            'file_urls' => [
                'copyright_image' => $this->getImageUrl('copyright_image'),
                'book_file' => $this->getFirstMediaUrl('book_file'),
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
