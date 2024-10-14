<?php

namespace App\Models;

use App\Traits\HasSingleImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class BookSeries extends Model implements HasMedia
{
    use HasFactory, HasSingleImage, HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
