<?php

namespace App\Models;

use App\Traits\HasSingleImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class AuthorRequest extends Model implements HasMedia
{
    use HasFactory, HasSingleImage;

    protected  $fillable = [
        'name',
        'biography',
        'birthdate',
        'user_id',
        'author_id',
        ];

    public  function user()
    {
        return $this->belongsTo(User::class);
    }

    public   function author()
    {
        return $this->belongsTo(Author::class);
    }
}
