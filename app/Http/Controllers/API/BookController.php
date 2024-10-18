<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index()
    {
        $search = request('search');
        $category_id = request('category_id');
        $latest = request('latest');
        $mostPopular = request('most_popular');
        $language = request('lang');
        $isApproved = request('is_approved');
        $publisherName = request('publisher_name');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $paginationSize = request('per_page', 10); // Allow per_page parameter

        $booksQuery = Book::with(['category', 'author']);

        // Search functionality
        if ($search) {
            $booksQuery->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('author', function ($authorQuery) use ($search) {
                        $authorQuery->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter by category
        if ($category_id) {
            $booksQuery->where('category_id', $category_id);
        }

        // Filter by language
        if ($language) {
            $booksQuery->where('lang', $language);
        }

        // Filter by approval status
        if ($isApproved !== null) {  // Expecting 0 or 1 for the approval filter
            $booksQuery->where('is_approved', $isApproved);
        }

        // Filter by publisher name
        if ($publisherName) {
            $booksQuery->where('publisher_name', 'LIKE', "%{$publisherName}%");
        }

        // Filter by date range (published_at)
        if ($dateFrom && $dateTo) {
            $booksQuery->whereBetween('published_at', [$dateFrom, $dateTo]);
        }

        // Latest books
        if ($latest) {
            $booksQuery->orderBy('published_at', 'desc');
        }

        // Most popular books
        if ($mostPopular === 'views') {
            $booksQuery->orderBy('views_count', 'desc');
        } elseif ($mostPopular === 'downloads') {
            $booksQuery->orderBy('downloads_count', 'desc');
        }


        // Pagination
        $books = $booksQuery->paginate($paginationSize);

        return BookResource::collection($books);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        // Authorization check
        $this->authorize('create', Book::class);

        // Create the book with validated data
        $book = Book::create($request->validated());

        // Handle media uploads
        $this->handleMediaUploads($request, $book);

        // Clear cache after book creation
        $this->clearCache();

        return new BookResource($book);
    }

    /**
     * Display the specified book.
     */
    public function show($id)
    {
        $cacheKey = "book_{$id}";
        $sessionKey = "viewed_books_{$id}";

        // Cache individual book details
        $book = Cache::rememberForever($cacheKey, function () use ($id) {
            return Book::with(['category', 'author'])->findOrFail($id);
        });

        // Authorization check
        $this->authorize('view', $book);

        // Check if the book has already been viewed in this session
        if (!session()->has($sessionKey)) {
            // Increment views_count only if not viewed in this session
            $book->increment('views_count');

            // Store the book ID in the session to prevent duplicate increments
            session()->put($sessionKey, true);
        }

        return new BookResource($book);
    }


    /**
     * Update the specified book in storage.
     */
    public function update(StoreBookRequest $request, Book $book)
    {
        // Authorization check
        $this->authorize('update', $book);

        // Update the book with validated data
        $book->update($request->validated());

        // Handle media updates
        $this->handleMediaUploads($request, $book);

        // Clear cache after book update
        $this->clearCache($book);

        return new BookResource($book);
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(Book $book)
    {
        // Authorization check
        $this->authorize('delete', $book);

        // Delete the book (media will be deleted automatically by Spatie)
        $book->delete();

        // Clear cache after book deletion
        $this->clearCache($book);

        return response()->json(['message' => 'Book and associated files deleted successfully.'], 200);
    }

    /**
     * Handle media uploads for cover_image and file (PDF).
     */
    private function handleMediaUploads($request, Book $book)
    {
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $book->clearMediaCollection('cover_image'); // Clear old image if any
            $book->addMedia($request->file('cover_image'))->toMediaCollection('cover_image');
        }

        // Handle file (PDF) upload
        if ($request->hasFile('file')) {
            $book->clearMediaCollection('file'); // Clear old file if any
            $book->addMedia($request->file('file'))->toMediaCollection('file');
        }
    }

    /**
     * Clear relevant cache for books.
     */
    private function clearCache($book = null)
    {
        // Clear the cache for the book list
        Cache::forget('books_list');

        // Clear the cache for a specific book, if provided
        if ($book) {
            Cache::forget("book_{$book->id}");
        }
    }
}
