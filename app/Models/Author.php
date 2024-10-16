<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Author extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('authors') 
            ->singleFile(); 
        $this->addMediaCollection('author_copyrights') 
            ->singleFile(); 
    }
}
