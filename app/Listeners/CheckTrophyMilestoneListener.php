<?php

namespace Fishinglog\Listeners;

use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Models\User;
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
                $recipients = collect();

                /** @var \Fishinglog\Models\User|null $primaryRecipient */
                $primaryRecipient = Auth::user() ?? $record->angler?->user;
                if ($primaryRecipient) {
                    $recipients->push($primaryRecipient);
                }

                // If this is an All-Time Record, Lake Record, or Personal Best, also notify Admin users for the Admin Notifications Hub
                if (in_array($milestone['type'] ?? '', ['all_time_record', 'lake_record', 'species_pb'])) {
                    $admins = User::where('type', User::ADMIN_TYPE)->get();
                    foreach ($admins as $admin) {
                        $recipients->push($admin);
                    }
                }

                $uniqueRecipients = $recipients->unique('id');
                foreach ($uniqueRecipients as $user) {
                    $user->notify(new TrophyCatchLogged($record, $milestone));
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
