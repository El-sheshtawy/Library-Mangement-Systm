<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'published_at' => $this->published_at,
            'is_approved' => $this->is_approved,
            'views_count' => $this->views_count,
            'downloads_count' => $this->downloads_count,
            'lang' => $this->lang,

            // Optimized category and author loading
            'category' => Cache::rememberForever('book_' . $this->id . '_category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'author' => Cache::rememberForever('book_' . $this->id . '_author', function () {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                ];
            }),

            // Caching cover image and file URLs
            'cover_image_url' => $this->getCachedMediaUrl('cover_image', 'cover_image_' . $this->id),
            'file_url' => $this->getCachedMediaUrl('file', 'file_url_' . $this->id),

            // Provide file extension
            'file_extension' => Cache::rememberForever('file_extension_' . $this->id, function () {
                $media = $this->getFirstMedia('file');
                return $media ? $media->mime_type : null;
            }),

            // Include thumbnail URL for the cover image
            'cover_image_thumbnail_url' => $this->getCachedMediaUrl('cover_image', 'cover_image_thumb_' . $this->id, 'thumb'),
        ];
    }

    /**
     * Get cached media URL for a collection with a specific conversion.
     *
     * @param string $collection
     * @param string $cacheKey
     * @param string|null $conversion
     * @return string|null
     */
    private function getCachedMediaUrl(string $collection, string $cacheKey, string $conversion = null): ?string
    {
        return Cache::rememberForever($cacheKey, function () use ($collection, $conversion) {
            return $this->getFirstMediaUrl($collection, $conversion) ?: null;
        });
    }
}
