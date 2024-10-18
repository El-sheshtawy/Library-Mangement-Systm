<?php

namespace App\Observers;

use App\Models\Author;
use Illuminate\Support\Facades\Cache;

class AuthorObserver
{
    /**
     * Handle the Author "updated" event.
     *
     * @param  Author  $author
     * @return void
     */
    public function updated(Author $author)
    {
        $this->clearCache($author);
    }

    /**
     * Handle the Author "deleted" event.
     *
     * @param  Author  $author
     * @return void
     */
    public function deleted(Author $author)
    {
        $this->clearCache($author);
    }

    /**
     * Clear cache for the given author.
     *
     * @param  Author  $author
     * @return void
     */
    protected function clearCache(Author $author)
    {
        // Clear cache for all books by this author
        foreach ($author->books as $book) {
            Cache::forget('book_' . $book->id . '_author');
        }
    }
}
