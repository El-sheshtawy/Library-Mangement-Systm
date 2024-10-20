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
            'real_views_count' => $this->real_views_count,
            'real_downloads_count' => $this->real_downloads_count,
            'fake_views_count' => $this->fake_views_count,
            'fake_downloads_count' => $this->fake_downloads_count,
            'lang' => $this->lang,

            // Optimized category and author loading
            'category' => $this->getCachedCategory(),
            'author' => $this->getCachedAuthor(),

            // Caching cover image and file URLs
            'cover_image_url' => $this->getCachedMediaUrl('cover_image', 'cover_image_' . $this->id),
            'cover_image_thumbnail_url' => $this->getCachedMediaUrl('cover_image', 'cover_image_thumb_' . $this->id, 'thumb'),

            // Provide file URL for downloading
            'file_url' => $this->getCachedMediaUrl('file', 'file_url_' . $this->id),

            // Provide file extension
            'file_extension' => $this->getCachedFileExtension(),

            // Optional download link, ensuring the file exists
            'download_link' => route('api.books.download', ['book' => $this->id]), // Add the download link
        ];
    }

    /**
     * Get cached category data.
     *
     * @return array|null
     */
    private function getCachedCategory(): ?array
    {
        return Cache::rememberForever('book_' . $this->id . '_category', function () {
            return $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null;
        });
    }

    /**
     * Get cached author data.
     *
     * @return array|null
     */
    private function getCachedAuthor(): ?array
    {
        return Cache::rememberForever('book_' . $this->id . '_author', function () {
            return $this->author ? [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ] : null;
        });
    }

    /**
     * Get cached file extension.
     *
     * @return string|null
     */
    private function getCachedFileExtension(): ?string
    {
        return Cache::rememberForever('file_extension_' . $this->id, function () {
            $media = $this->getFirstMedia('file');
            return $media ? $media->mime_type : null;
        });
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
