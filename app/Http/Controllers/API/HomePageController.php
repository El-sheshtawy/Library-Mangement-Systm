<?php

namespace App\Http\Controllers\API;

use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class HomePageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(Request $request)
    {
        $cacheKey = $this->generateCacheKey($request);

        // Store cache keys for easier invalidation later
        $this->storeCacheKey($cacheKey);

        // Cache the books list forever (until manually invalidated)
        $books = Cache::rememberForever($cacheKey, function () use ($request) {
            return Book::with(['category', 'author'])
                ->when($request->input('search'), function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhereHas('category', function ($q) use ($search) {
                                $q->where('name', 'like', '%' . $search . '%');
                            })
                            ->orWhereHas('author', function ($q) use ($search) {
                                $q->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->when($request->input('filter'), function ($query, $filter) {
                    switch ($filter) {
                        case 'latest':
                            $query->orderBy('published_at', 'desc');
                            break;
                        case 'popular':
                            $query->orderBy('views_count', 'desc');
                            break;
                        case 'downloads':
                            $query->orderBy('downloads_count', 'desc');
                            break;
                        default:
                            $query->inRandomOrder();
                            break;
                    }
                })
                ->paginate(12); 
        });

        return BookResource::collection($books);
    }

    /**
     * Generate a unique cache key for the request.
     *
     * @param Request $request
     * @return string
     */
    private function generateCacheKey(Request $request): string
    {
        return 'books_' . md5($request->fullUrl());
    }

    /**
     * Store cache key for invalidation purposes.
     *
     * @param string $cacheKey
     * @return void
     */
    private function storeCacheKey(string $cacheKey): void
    {
        $keys = Cache::get('book_cache_keys', []);

        if (!in_array($cacheKey, $keys)) {
            $keys[] = $cacheKey;
            Cache::forever('book_cache_keys', $keys);
        }
    }
}
