<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Expedition;
use Illuminate\Support\Carbon;

class ExpeditionDiscoveryService
{
    /**
     * Find an existing Expedition covering the given date with optional grace window.
     *
     * @param Carbon|string|null $date
     * @param int $graceDays
     * @return Expedition|null
     */
    public function findExpeditionForDate(Carbon|string|null $date, int $graceDays = 2): ?Expedition
    {
        if (!$date) {
            return null;
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $searchStart = (clone $carbon)->subDays($graceDays)->startOfDay();
        $searchFinish = (clone $carbon)->addDays($graceDays)->endOfDay();

        return Expedition::where(function ($query) use ($searchStart, $searchFinish) {
            $query->where('start', '<=', $searchFinish)
                ->where('finish', '>=', $searchStart);
        })->first();
    }
}

