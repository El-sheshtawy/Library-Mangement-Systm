<?php

namespace App\Models;

use App\Constants\MediaConstants;
use App\Traits\HasSingleImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class Author extends Model implements HasMedia
{
    use HasFactory, HasSingleImage;

    protected  $fillable = [
        'name',
        'biography',
        'birthdate',
        ];

    public  function books()
    {
        return $this->hasMany(Book::class);
    }

    public  function authorRequests()
    {
        return $this->hasMany(AuthorRequest::class);
    }

    /**
     * Specify a custom default image path.
     *
     * @return string
     */
    public function defaultImagePath(): string
    {
        return MediaConstants::DEFAULT_AUTHOR_IMAGE; // Define in MediaConstants
    }
}
