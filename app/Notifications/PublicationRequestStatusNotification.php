<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\PublicationRequest;

class PublicationRequestStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $publicationRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(PublicationRequest $publicationRequest)
    {
        $this->publicationRequest = $publicationRequest;
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'status' => $this->publicationRequest->status,
            'publish' => $this->publicationRequest->publish,
            'book_id' => $this->publicationRequest->book_id,
            'book_title' => $this->publicationRequest->book->title,
        ];
    }
}
