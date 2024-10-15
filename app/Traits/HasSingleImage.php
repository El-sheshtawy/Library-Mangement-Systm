<?php

namespace App\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;
use App\Constants\MediaConstants;

trait HasSingleImage
{
    use InteractsWithMedia;

    /**
     * Define media collections.
     *
     * Each model using this trait will have a single image collection named 'image'.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('image')
            ->singleFile()
            ->useFallbackUrl('/assets/images/static/person.png')
            ->useFallbackPath(public_path('/assets/images/static/person.png'))
            ->useDisk('public'); // or 'private' based on needs
    }

    /**
     * Define media conversions.
     *
     * @return void
     */
    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);

        $this
            ->addMediaConversion('medium')
            ->width(300)
            ->height(300)
            ->sharpen(10);
    }

    /**
     * Get the URL of the image with a specific conversion.
     *
     * @param string $conversion
     * @return string
     */
    public function getImageUrl(string $conversion = ''): string
    {
        if ($conversion) {
            return $this->getFirstMediaUrl('image', $conversion);
        }

        return $this->getFirstMediaUrl('image');
    }
}
