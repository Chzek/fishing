<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\JournalEntry;
use Fishinglog\Models\Lake;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

    /**
     * Analyze journal entry dates and recommend missing Expeditions based on date clusters.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getRecommendedExpeditions(): Collection
    {
        $unlinkedEntries = JournalEntry::whereNull('expeditions_id')
            ->whereNotNull('entry_date')
            ->orderBy('entry_date')
            ->with(['anglers', 'lakes', 'pages'])
            ->get();

        if ($unlinkedEntries->isEmpty()) {
            return collect([]);
        }

        // 1. Cluster dates into continuous trips (max 4 day gap between entries in same trip)
        $clusters = [];
        $currentCluster = [];

        foreach ($unlinkedEntries as $entry) {
            if (empty($currentCluster)) {
                $currentCluster[] = $entry;
                continue;
            }

            /** @var JournalEntry $lastEntry */
            $lastEntry = end($currentCluster);
            $daysDiff = $entry->entry_date->diffInDays($lastEntry->entry_date);

            if ($daysDiff <= 5) {
                $currentCluster[] = $entry;
            } else {
                $clusters[] = $currentCluster;
                $currentCluster = [$entry];
            }
        }

        if (!empty($currentCluster)) {
            $clusters[] = $currentCluster;
        }

        // 2. Build recommendation objects for clusters without matching Expeditions
        $recommendations = collect([]);

        foreach ($clusters as $cluster) {
            /** @var Collection<int, JournalEntry> $clusterEntries */
            $clusterEntries = collect($cluster);
            $startDate = $clusterEntries->min('entry_date');
            $endDate = $clusterEntries->max('entry_date');

            if (!$startDate || !$endDate) {
                continue;
            }

            // Check if an existing expedition already covers this window
            $existing = $this->findExpeditionForDate($startDate, 3);
            if ($existing) {
                // Auto-link existing expedition
                foreach ($clusterEntries as $entry) {
                    $entry->update(['expeditions_id' => $existing->id]);
                }
                continue;
            }

            // Collect distinct anglers and lakes mentioned across the entries
            $anglers = $clusterEntries->flatMap->anglers->unique('id')->values();
            $lakes = $clusterEntries->flatMap->lakes->unique('id')->values();

            $season = $this->resolveSeasonName($startDate);
            $suggestedTitle = "Canada {$season} Expedition {$startDate->format('Y')}";

            $recommendations->push([
                'suggested_title' => $suggestedTitle,
                'start_date' => $startDate->format('Y-m-d'),
                'finish_date' => $endDate->format('Y-m-d'),
                'days_count' => $startDate->diffInDays($endDate) + 1,
                'entries_count' => $clusterEntries->count(),
                'entry_ids' => $clusterEntries->pluck('id')->all(),
                'matched_anglers' => $anglers,
                'matched_lakes' => $lakes,
            ]);
        }

        return $recommendations;
    }

    /**
     * Create an Expedition from a recommendation payload and link all cluster entries & crew.
     *
     * @param array<string, mixed> $recommendation
     * @return Expedition
     */
    public function createFromRecommendation(array $recommendation): Expedition
    {
        $startDate = Carbon::parse($recommendation['start_date']);
        $finishDate = Carbon::parse($recommendation['finish_date']);

        $expedition = Expedition::create([
            'description' => $recommendation['suggested_title'] ?? "Expedition {$startDate->format('Y')}",
            'start' => $startDate,
            'finish' => $finishDate,
        ]);

        // Link journal entries
        if (!empty($recommendation['entry_ids'])) {
            JournalEntry::whereIn('id', $recommendation['entry_ids'])->update([
                'expeditions_id' => $expedition->id,
            ]);
        }

        // Add matched anglers to crew roster
        if (!empty($recommendation['matched_anglers'])) {
            foreach ($recommendation['matched_anglers'] as $angler) {
                $anglerId = is_array($angler) ? ($angler['id'] ?? null) : ($angler->id ?? null);
                if ($anglerId) {
                    Crew::firstOrCreate([
                        'expeditions_id' => $expedition->id,
                        'anglers_id' => $anglerId,
                    ]);
                }
            }
        }

        return $expedition;
    }

    /**
     * Resolve season name based on month.
     *
     * @param Carbon $date
     * @return string
     */
    protected function resolveSeasonName(Carbon $date): string
    {
        $month = $date->month;
        return match (true) {
            $month >= 5 && $month <= 6 => 'Spring',
            $month >= 7 && $month <= 8 => 'Summer',
            $month >= 9 && $month <= 10 => 'Fall',
            default => 'Winter',
        };
    }
}
