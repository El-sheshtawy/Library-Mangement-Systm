<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AuthorRequest extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'name',
        'biography',
        'birthdate',
        'status',
        'user_id',
        'author_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('author_requests') 
            ->singleFile(); 
        $this->addMediaCollection('copyright') 
            ->singleFile(); 
    }
}
