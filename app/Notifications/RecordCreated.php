<?php

namespace Fishinglog\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecordCreated extends Notification
{
    use Queueable;

    protected $record;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\Fishinglog\Record $record)
    {
        $this->record = $record;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'record' => $this->record,
        ];
    }
}
