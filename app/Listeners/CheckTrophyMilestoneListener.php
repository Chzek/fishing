<?php

namespace Fishinglog\Listeners;

use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Notifications\TrophyCatchLogged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckTrophyMilestoneListener
{
    /**
     * Handle the event.
     *
     * @param CatchLoggedEvent $event
     * @return void
     */
    public function handle(CatchLoggedEvent $event): void
    {
        try {
            $record = $event->record;
            $milestone = $record->checkTrophyMilestone();

            if ($milestone) {
                /** @var \Fishinglog\Models\User|null $recipient */
                $recipient = Auth::user() ?? $record->angler?->user;
                if ($recipient) {
                    $recipient->notify(new TrophyCatchLogged($record, $milestone));
                }

                if (function_exists('session') && session()->isStarted()) {
                    session()->flash('trophy_celebration', array_merge($milestone, [
                        'record_id' => $record->id,
                        'species_name' => $record->fishBreed ? $record->fishBreed->name : 'Fish',
                        'length' => $record->length,
                        'lake_name' => $record->lake ? $record->lake->name : 'Waterbody',
                    ]));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to evaluate trophy milestone in CheckTrophyMilestoneListener: ' . $e->getMessage());
        }
    }
}
