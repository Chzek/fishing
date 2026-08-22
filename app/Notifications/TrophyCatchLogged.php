<?php

namespace Fishinglog\Notifications;

use Fishinglog\Models\Record;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrophyCatchLogged extends Notification
{
    use Queueable;

    public Record $record;
    public array $milestone;

    /**
     * Create a new trophy catch notification instance.
     *
     * @param \Fishinglog\Models\Record $record
     * @param array $milestone
     */
    public function __construct(Record $record, array $milestone = [])
    {
        $this->record = $record;
        $this->milestone = $milestone;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        $species = $this->record->fishBreed?->name ?? 'Fish';
        $lake = $this->record->lake?->name ?? 'Waterbody';
        $length = $this->record->length ? $this->record->length . '"' : '';

        $title = $this->milestone['title'] ?? "🏆 New Personal Best {$species}!";
        $previousText = isset($this->milestone['previous_length']) && $this->milestone['previous_length'] > 0
            ? " (beat previous record of {$this->milestone['previous_length']}\")"
            : '';

        $message = "You recorded a new Personal Best {$species} ({$length}) at {$lake}{$previousText}!";

        return [
            'type' => 'trophy_catch',
            'record_id' => $this->record->id,
            'milestone_type' => $this->milestone['type'] ?? 'species_pb',
            'title' => $title,
            'species_name' => $species,
            'length' => $this->record->length,
            'weight' => $this->record->weight,
            'lake_name' => $lake,
            'message' => $message,
            'action_url' => url('/record/' . $this->record->id),
        ];
    }
}
