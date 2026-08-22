<?php

namespace Fishinglog\Notifications;

use Fishinglog\Models\Record;
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
     * @param \Fishinglog\Models\Record $record
     * @return void
     */
    public function __construct(Record $record)
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
            'type' => 'record_created',
            'record_id' => $this->record->id,
            'species_name' => $this->record->fishBreed?->name ?? 'Fish',
            'length' => $this->record->length,
            'weight' => $this->record->weight,
            'lake_name' => $this->record->lake?->name ?? 'Waterbody',
            'message' => "New catch recorded: " . ($this->record->length ? $this->record->length . '" ' : '') . ($this->record->fishBreed?->name ?? 'Fish') . " at " . ($this->record->lake?->name ?? 'Waterbody') . ".",
            'action_url' => url('/record/' . $this->record->id),
        ];
    }

}
