<?php

namespace App\Observers;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookObserver
{
    /**
     * Handle the Book "created" event.
     *
     * @param  Book  $book
     * @return void
     */
    public function created(Book $book)
    {
        $this->clearCache($book);
    }

    /**
     * Handle the Book "updated" event.
     *
     * @param  Book  $book
     * @return void
     */
    public function updated(Book $book)
    {
        $this->clearCache($book);
    }

    /**
     * Handle the Book "deleted" event.
     *
     * @param  Book  $book
     * @return void
     */
    public function deleted(Book $book)
    {
        $this->clearCache($book);
    }

    /**
     * Clear cache for the given book.
     *
     * @param  Book  $book
     * @return void
     */
    protected function clearCache(Book $book)
    {
        Cache::forget('cover_image_' . $book->id);
        Cache::forget('file_url_' . $book->id);
        Cache::forget('file_extension_' . $book->id);
        Cache::forget('cover_image_thumb_' . $book->id);
        Cache::forget('book_' . $book->id . '_category');
        Cache::forget('book_' . $book->id . '_author');
    }
}
