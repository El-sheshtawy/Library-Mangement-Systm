<?php

namespace App\Actions;

use App\Models\Book;
use App\Models\User;
use App\Notifications\NewOrPopularBookNotification;

class MarkBookAsPopularAction
{
    /**
     * @param Book $book
     * @return \Illuminate\Http\JsonResponse
     *
     * Accessing Notifications
     * Users can access the notifications from their profile or dashboard. You can retrieve the notifications like this:
     *
     * $user = auth()->user();
     * $notifications = $user->notifications;
     * Or, to get only unread notifications:
     *
     * $unreadNotifications = $user->unreadNotifications;
     *
     */
    public function markBookAsPopular(Book $book)
    {
        // Assume this method is called when a book becomes popular
        if ($book->views_count > 1000 || $book->downloads_count > 500) {
            // Send notification to all users about the popular book
            $users = User::all();  // Or select a specific group of users
            foreach ($users as $user) {
                $user->notify(new NewOrPopularBookNotification($book, 'popular'));
            }
        }

        return response()->json(['message' => 'Popular book notification sent to users.']);
    }

}
