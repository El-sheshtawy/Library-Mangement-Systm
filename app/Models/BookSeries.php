<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class BookSeries extends Model implements HasMedia
{
    use HasFactory, HasImage;

    protected $fillable = [
        'title',
        'description',
        'user_id'
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function registerMediaCollections(): void
    {
        // Profile Image
        $this->registerImageCollection(
            'book_series',
            true,
            url('/assets/images/static/book_series.png'),
            public_path('/assets/images/static/book_series.png')
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
