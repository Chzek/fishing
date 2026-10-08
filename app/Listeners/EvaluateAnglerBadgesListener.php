<?php

namespace Fishinglog\Listeners;

use Fishinglog\Events\CatchLoggedEvent;
use Fishinglog\Services\BadgeEvaluatorService;
use Illuminate\Support\Facades\Log;

class EvaluateAnglerBadgesListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected BadgeEvaluatorService $badgeEvaluatorService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(CatchLoggedEvent $event): void
    {
        try {
            $record = $event->record;
            $angler = $record->angler;

            if (!$angler) {
                return;
            }

            $newlyAwarded = $this->badgeEvaluatorService->evaluateAngler($angler, $record);

            if ($newlyAwarded->isNotEmpty()) {
                Log::info('Awarded merit badges to angler', [
                    'angler_id' => $angler->id,
                    'badges_count' => $newlyAwarded->count(),
                    'badge_ids' => $newlyAwarded->pluck('badge_id')->all(),
                ]);

                // Flash awarded badges to session for celebrative modal / toast
                if (session()) {
                    session()->flash('unlocked_badges', $newlyAwarded->map(fn ($ab) => $ab->load('badge'))->toArray());
                }
            }
        } catch (\Throwable $e) {
            Log::error('Failed to evaluate angler badges on CatchLoggedEvent', [
                'record_id' => $event->record->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
