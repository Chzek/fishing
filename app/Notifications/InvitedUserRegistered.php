<?php

namespace Fishinglog\Notifications;

use Fishinglog\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvitedUserRegistered extends Notification
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'user_registered',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'message' => "New user {$this->user->name} ({$this->user->email}) has registered. Please link them to an Angler profile in User Accounts.",
            'action_url' => route('admin.users'),
        ];
    }
}
