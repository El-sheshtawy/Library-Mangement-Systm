<?php

namespace App\Models;

use App\Traits\HasSingleImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PublicationRequest extends Model implements HasMedia
{
    use HasFactory, HasSingleImage;

    protected  $fillable = [
        'publish',
        'status',
        'user_id',
        'book_id',
    ];

    public  function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

}
