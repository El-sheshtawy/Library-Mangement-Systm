<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Book;

class NewOrPopularBookNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $book;
    protected $type; // To distinguish between new or popular book

    /**
     * Create a new notification instance.
     */
    public function __construct(Book $book, string $type)
    {
        $this->book = $book;
        $this->type = $type;
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'book_id' => $this->book->id,
            'title' => $this->book->title,
            'author' => $this->book->author->name,
            'type' => $this->type, // new or popular
        ];
    }

    // for call simulate the usage of the logic
//    public function changeUserRole(User $user, Role $role)
//    {
//        // Assuming roles are synced or attached in a many-to-many relationship
//        $user->roles()->sync([$role->id]);
//
//        // Notify the user about their role change
//        $user->notify(new RoleChangedNotification($user, $role));
//
//        return response()->json(['message' => 'Role changed and user notified.']);
//    }

    // optional if want to implement it by email
    /**
     * Get the notification message for mail (optional if you want to send email too).
     */
//    public function toMail($notifiable): MailMessage
//    {
//        return (new MailMessage)
//            ->subject('New or Popular Book Published')
//            ->line("A {$this->type} book titled '{$this->book->title}' has just been published!")
//            ->action('View Book', url("/books/{$this->book->id}"))
//            ->line('Thank you for using our platform!');
//    }
}
