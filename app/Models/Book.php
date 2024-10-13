<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

     protected $fillable = [
        'title', 
        'description', 
        'file',
        'publish', 
        'cover_image', 
        'size', 
        'number_pages', 
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

    public  function author()
    {
        return $this->belongsTo(Author::class);
    }

}
