<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    // Store a new comment for a book
    public function store(Request $request, $bookId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'rating' => 'required|integer|between:1,5',
        ]);

        $book = Book::findOrFail($bookId);

        $comment = $book->comments()->create([
            'content' => $validated['content'],
            'rating' => $validated['rating'],
            'user_id' => auth()->id(),
            'status' => 1,
        ]);

        return new CommentResource($comment);
    }

    // Update an existing comment
    public function update(Request $request, $bookId, $commentId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'rating' => 'required|integer|between:1,5',
        ]);

        $comment = Comment::where('book_id', $bookId)
            ->where('id', $commentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $comment->update($validated);

        return new CommentResource($comment);
    }

    // Delete a comment
    public function destroy($bookId, $commentId)
    {
        $comment = Comment::where('book_id', $bookId)
            ->where('id', $commentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }

    // Show comments for a book
    public function index($bookId)
    {
        $book = Book::findOrFail($bookId);
        $comments = $book->comments()->where('status', 1)->get();

        return CommentResource::collection($comments);
    }
}
