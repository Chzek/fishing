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
        $this->record->loadMissing(['fishBreed', 'lake', 'angler.user']);
        $species = $this->record->fishBreed ? $this->record->fishBreed->name : 'Fish';
        $lake = $this->record->lake ? $this->record->lake->name : 'Waterbody';
        $length = $this->record->length ? $this->record->length . '"' : '';
        $anglerName = $this->record->angler ? $this->record->angler->fullName : 'An angler';

        $isCatchingAngler = (isset($notifiable->id) && $this->record->angler?->user_id === $notifiable->id);

        $milestoneType = $this->milestone['type'] ?? 'species_pb';
        $title = $this->milestone['title'] ?? "🏆 New Personal Best {$species}!";

        $previousText = isset($this->milestone['previous_length']) && $this->milestone['previous_length'] > 0
            ? " (beat previous record of {$this->milestone['previous_length']}\")"
            : '';

        $message = match ($milestoneType) {
            'all_time_record' => $isCatchingAngler
                ? "You caught a new All-Time Logbook Record {$species} ({$length}) at {$lake}{$previousText}!"
                : "{$anglerName} caught a new All-Time Logbook Record {$species} ({$length}) at {$lake}{$previousText}!",
            'lake_record' => $isCatchingAngler
                ? "You set a new {$lake} Record for {$species} ({$length}){$previousText}!"
                : "{$anglerName} set a new {$lake} Record for {$species} ({$length}){$previousText}!",
            'first_species_catch' => $isCatchingAngler
                ? "You logged your first {$species} ({$length}) at {$lake}!"
                : "{$anglerName} logged their first {$species} ({$length}) at {$lake}!",
            default => $isCatchingAngler
                ? "You recorded a new Personal Best {$species} ({$length}) at {$lake}{$previousText}!"
                : "{$anglerName} recorded a new Personal Best {$species} ({$length}) at {$lake}{$previousText}!",
        };

        return [
            'type' => 'trophy_catch',
            'record_id' => $this->record->id,
            'milestone_type' => $milestoneType,
            'title' => $title,
            'angler_name' => $anglerName,
            'species_name' => $species,
            'length' => $this->record->length,
            'weight' => $this->record->weight,
            'lake_name' => $lake,
            'message' => $message,
            'action_url' => url('/record/' . $this->record->id),
        ];
    }
}
