<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    /**
     * Handle the Category "updated" event.
     *
     * @param  Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        $this->clearCache($category);
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        $this->clearCache($category);
    }

    /**
     * Clear cache for the given category.
     *
     * @param  Category  $category
     * @return void
     */
    protected function clearCache(Category $category)
    {
        // Clear cache for all books in this category
        foreach ($category->books as $book) {
            Cache::forget('book_' . $book->id . '_category');
        }
    }
}
