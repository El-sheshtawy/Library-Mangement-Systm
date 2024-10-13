<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Role;
use App\Models\User;

class RoleChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $role;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'name' => $this->user->name,
            'new_role' => $this->role->name,
        ];
    }

    // optional if want to implement it by email
//    /**
//     * Get the notification message for mail (optional if you want to send an email).
//     */
//    public function toMail($notifiable): MailMessage
//    {
//        return (new MailMessage)
//            ->subject('Role Changed')
//            ->line("Your role has been changed to {$this->role->name}.")
//            ->action('View Your Profile', url("/profile/{$this->user->id}"))
//            ->line('Thank you for using our platform!');
//    }
}
