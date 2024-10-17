<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Book extends Model implements HasMedia
{
    use HasFactory, HasImage;

    protected $fillable = [
        'title',
        'description',
        'publisher_name',
        'published_at',
        'is_approved',
        'lang',
        'downloads_count',
        'views_count',
        'book_series_id',
        'category_id',
        'user_id',
        'author_id',
    ];

    // Relationships
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function publicationRequest()
    {
        return $this->hasOne(PublicationRequest::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Register media collections for Spatie's Media Library.
     */
    public function registerMediaCollections(): void
    {
        // Register the cover image collection
        $this->addMediaCollection('cover_image')
            ->singleFile(); // Only one cover image can be uploaded

        // Register the file (PDF) collection
        $this->addMediaCollection('file')
            ->useDisk('public')  // Define the disk to use
            ->singleFile();  // Only one file can be uploaded
    }
}
