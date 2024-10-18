<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Author extends Model implements HasMedia
{
    use HasImage, HasFactory;

    protected $fillable = [
        'name',
        'biography',
        'birthdate',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function authorRequests()
    {
        return $this->hasMany(AuthorRequest::class);
    }

    /**
     * Register media collections for Author.
     */
    public function registerMediaCollections(): void
    {
        // Profile Image
        $this->registerImageCollection(
            'author_image',
            true,
            url('/assets/images/static/person.png'),
            public_path('/assets/images/static/person.png')
        );
    }
}
