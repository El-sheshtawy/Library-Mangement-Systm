<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BookSeries extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
        $this->addMediaCollection('images')
                    ->singleFile(); ;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
