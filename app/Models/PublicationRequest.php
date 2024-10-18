<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationRequest extends Model
{
    use HasFactory, HasImage;

    protected  $fillable = [
        'publisher_name',
        'status',
        'user_id',
        'book_id',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Register media collections for User.
     */
    /**
     * Register media collections for the PublicationRequest.
     */
    public function registerMediaCollections(): void
    {
        // Register the copyright image collection
        $this->registerImageCollection(
            'copyright_image', // Collection name
            true // Single file required
        );

        // Register the book file collection
        $this->addMediaCollection('book_file')
            ->useDisk('public') // Adjust the disk if needed
            ->singleFile(); // Single file only
    }

}
