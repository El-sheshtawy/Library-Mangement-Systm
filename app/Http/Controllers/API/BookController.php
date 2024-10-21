<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreBookRequest;
use App\Jobs\DownloadBookPdf;
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
        $trending= request('trending');
        $language = request('lang');
        $isApproved = request('is_approved');
        $publisherName = request('publisher_name');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $paginationSize = request('per_page', 10); // Allow per_page parameter

        $booksQuery = Book::with(['category', 'author']);

        // Restrict access for users with the 'user' role
        if (auth()->user()->role->name === 'super_admin') {
            $booksQuery->where('user_id', auth()->id());
        }

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

        // Most Trending books (Based on real views or downloads)
        if ($trending === 'trending') {
            $booksQuery->orderBy('real_views_count', 'desc');
        } elseif ($mostPopular === 'trending_downloads') {
            $booksQuery->orderBy('real_downloads_count', 'desc');
        }

        // Most popular books (Based on fake views or downloads)
        if ($mostPopular === 'popular') {
            $booksQuery->orderBy('fake_views_count', 'desc');
        } elseif ($mostPopular === 'popular_downloads') {
            $booksQuery->orderBy('fake_downloads_count', 'desc');
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

        $book = Cache::rememberForever($cacheKey, function () use ($id) {
            return Book::with(['category', 'author', 'comments.user'])->findOrFail($id);
        });

        $this->authorize('view', $book);

        if (!session()->has($sessionKey)) {
            $book->increment('real_views_count');
            $book->increment('fake_views_count');
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

    /**
     * Dispatch the download job for the book.
     *
     * @param Book $book
     * @return \Illuminate\Http\JsonResponse
     */
    public function download(Book $book)
    {
        try {
            // Dispatch the job to handle the PDF download in the queue
            DownloadBookPdf::dispatch($book);

            return response()->json([
                'message' => 'Your download is being processed and will start shortly.'
            ], 202); // Return 202 Accepted as the process is queued
        } catch (\Exception $e) {
            return response()->json(['error' => 'File not found'], 404);
        }
    }
}
