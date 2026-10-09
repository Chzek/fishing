<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Angler;
use Fishinglog\Models\AnglerBadge;
use Fishinglog\Models\Badge;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BadgeEvaluatorService
{
    /**
     * Evaluate all unearned badges for an angler and award eligible ones.
     *
     * @param Angler $angler
     * @param Record|null $triggerRecord
     * @return Collection<int, AnglerBadge>
     */
    public function evaluateAngler(Angler $angler, ?Record $triggerRecord = null): Collection
    {
        $earnedBadgeIds = AnglerBadge::where('anglers_id', $angler->id)->pluck('badge_id')->all();

        /** @var EloquentCollection<int, Badge> $candidateBadges */
        $candidateBadges = Badge::whereNotIn('id', $earnedBadgeIds)
            ->orderBy('sort_order')
            ->get();

        if ($candidateBadges->isEmpty()) {
            return collect();
        }

        // Preload angler records chronologically with related models
        /** @var EloquentCollection<int, Record> $records */
        $records = Record::where('anglers_id', $angler->id)
            ->with(['lake.fishingZone', 'fishBreed.family', 'expedition'])
            ->orderBy('caught', 'asc')
            ->get();

        // Preload expeditions the angler was a crew member on
        $expeditions = Expedition::whereHas('crews', function ($query) use ($angler) {
            $query->where('anglers_id', $angler->id);
        })->with(['crews.records', 'records'])->get();

        $newlyAwarded = collect();

        foreach ($candidateBadges as $badge) {
            $qualification = $this->qualifyBadge($badge, $angler, $records, $expeditions);

            if ($qualification !== null) {
                $awarded = $this->awardBadge($angler, $badge, $qualification);
                $newlyAwarded->push($awarded);
            }
        }

        return $newlyAwarded;
    }

    /**
     * Recalculate badges across all anglers or a specific angler.
     *
     * @param Angler|null $specificAngler
     * @param bool $force Clear existing earned badges first if true
     * @return array{anglers_processed: int, badges_awarded: int}
     */
    public function recalculateAll(?Angler $specificAngler = null, bool $force = false): array
    {
        $anglersQuery = $specificAngler ? Angler::where('id', $specificAngler->id) : Angler::query();
        $anglers = $anglersQuery->get();

        $badgesAwardedCount = 0;

        foreach ($anglers as $angler) {
            if ($force) {
                AnglerBadge::where('anglers_id', $angler->id)->delete();
            }

            $awarded = $this->evaluateAngler($angler);
            $badgesAwardedCount += $awarded->count();
        }

        return [
            'anglers_processed' => $anglers->count(),
            'badges_awarded' => $badgesAwardedCount,
        ];
    }

    /**
     * Check if the angler qualifies for the given badge.
     *
     * @param Badge $badge
     * @param Angler $angler
     * @param EloquentCollection<int, Record> $records
     * @param EloquentCollection<int, Expedition> $expeditions
     * @return array{record: Record|null, expedition: Expedition|null, awarded_at: Carbon, summary: string}|null
     */
    protected function qualifyBadge(
        Badge $badge,
        Angler $angler,
        EloquentCollection $records,
        EloquentCollection $expeditions
    ): ?array {
        $threshold = (float) $badge->rule_threshold;

        return match ($badge->rule_type) {
            'volume_total' => $this->evaluateVolume($badge, $records, (int) $threshold),
            'species_count' => $this->evaluateSpeciesCount($badge, $records, (int) $threshold),
            'diversity_distinct' => $this->evaluateDiversity($badge, $records, (int) $threshold),
            'diversity_families' => $this->evaluateDiversityFamilies($badge, $records, (int) $threshold),
            'cadence_distinct_days' => $this->evaluateCadence($badge, $records, (int) $threshold),
            'streak_years' => $this->evaluateStreak($badge, $records, (int) $threshold),
            'lakes_distinct' => $this->evaluateDistinctLakes($badge, $records, (int) $threshold),
            'lake_superior_catch' => $this->evaluateLakeSuperior($badge, $records),
            'new_water_first' => $this->evaluateNewWater($badge, $angler, $records),
            'weather_pressure_drop' => $this->evaluateWeatherPressureDrop($badge, $records),
            'weather_storm_rain' => $this->evaluateWeatherStorm($badge, $records),
            'fmz_distinct' => $this->evaluateFmzDistinct($badge, $records, (int) $threshold),
            'cumulative_inches' => $this->evaluateCumulativeInches($badge, $records, $threshold),
            'released_catches' => $this->evaluateReleasedCatches($badge, $records, (int) $threshold),
            'skunked_trip' => $this->evaluateSkunkedTrip($badge, $records, $expeditions),
            'crew_rank_first' => $this->evaluateCrewRank($badge, $angler, $expeditions, 1),
            'crew_rank_second' => $this->evaluateCrewRank($badge, $angler, $expeditions, 2),
            default => null,
        };
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateVolume(Badge $badge, EloquentCollection $records, int $threshold): ?array
    {
        if ($records->count() >= $threshold) {
            $qualifyingRecord = $records->get($threshold - 1);
            $awardedAt = $qualifyingRecord && $qualifyingRecord->caught ? $qualifyingRecord->caught : now();

            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord?->expedition,
                'awarded_at' => $awardedAt,
                'summary' => "Career catch #{$threshold}: " . $this->formatRecordSummary($qualifyingRecord),
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateSpeciesCount(Badge $badge, EloquentCollection $records, int $threshold): ?array
    {
        $targetSpecies = match (true) {
            str_contains($badge->slug, 'walleye') => 'Walleye',
            str_contains($badge->slug, 'pike') => 'Northern Pike',
            str_contains($badge->slug, 'bass') => 'Smallmouth Bass',
            str_contains($badge->slug, 'laker') => 'Lake Trout',
            default => null,
        };

        if ($targetSpecies === null) {
            return null;
        }

        $matchingRecords = $records->filter(function (Record $record) use ($targetSpecies) {
            return $record->fishBreed && str_contains(strtolower($record->fishBreed->name), strtolower($targetSpecies));
        })->values();

        if ($matchingRecords->count() >= $threshold) {
            $qualifyingRecord = $matchingRecords->get($threshold - 1);
            $awardedAt = $qualifyingRecord && $qualifyingRecord->caught ? $qualifyingRecord->caught : now();

            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord?->expedition,
                'awarded_at' => $awardedAt,
                'summary' => "Verified {$targetSpecies} #{$threshold}: " . $this->formatRecordSummary($qualifyingRecord),
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateDiversity(Badge $badge, EloquentCollection $records, int $threshold): ?array
    {
        $seenBreeds = [];
        $qualifyingRecord = null;

        foreach ($records as $record) {
            if ($record->fish_breeds_id && !in_array($record->fish_breeds_id, $seenBreeds, true)) {
                $seenBreeds[] = $record->fish_breeds_id;
                if (count($seenBreeds) === $threshold) {
                    $qualifyingRecord = $record;
                    break;
                }
            }
        }

        if ($qualifyingRecord !== null) {
            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord->expedition,
                'awarded_at' => $qualifyingRecord->caught ?? now(),
                'summary' => "Diversity milestone reached ({$threshold} distinct species): " . $this->formatRecordSummary($qualifyingRecord),
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     * @return array{record: Record|null, expedition: Expedition|null, awarded_at: Carbon, summary: string}|null
     */
    protected function evaluateDiversityFamilies(Badge $badge, EloquentCollection $records, int $threshold): ?array
    {
        $seenFamilies = [];
        $qualifyingRecord = null;

        foreach ($records as $record) {
            $familyId = $record->fishBreed?->fish_families_id;
            if ($familyId && !in_array($familyId, $seenFamilies, true)) {
                $seenFamilies[] = $familyId;
                if (count($seenFamilies) === $threshold) {
                    $qualifyingRecord = $record;
                    break;
                }
            }
        }

        if ($qualifyingRecord !== null) {
            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord->expedition,
                'awarded_at' => $qualifyingRecord->caught ?? now(),
                'summary' => "DEI Specialist unlocked ({$threshold} distinct fish families): " . $this->formatRecordSummary($qualifyingRecord),
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateCadence(Badge $badge, EloquentCollection $records, int $threshold): ?array
    {
        $seenDays = [];
        $qualifyingRecord = null;

        foreach ($records as $record) {
            $dayKey = $record->caught?->toDateString() ?? $record->caught_date;
            if ($dayKey && !in_array($dayKey, $seenDays, true)) {
                $seenDays[] = $dayKey;
                if (count($seenDays) === $threshold) {
                    $qualifyingRecord = $record;
                    break;
                }
            }
        }

        if ($qualifyingRecord !== null) {
            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord->expedition,
                'awarded_at' => $qualifyingRecord->caught ?? now(),
                'summary' => "Day #{$threshold} logged on water: " . $this->formatRecordSummary($qualifyingRecord),
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateStreak(Badge $badge, EloquentCollection $records, int $threshold): ?array
    {
        $years = $records->map(fn (Record $r) => $r->caught ? $r->caught->year : null)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (count($years) < $threshold) {
            return null;
        }

        $maxConsecutive = 1;
        $currentStreak = 1;
        $streakEndYear = $years[0];

        for ($i = 1; $i < count($years); $i++) {
            if ($years[$i] === $years[$i - 1] + 1) {
                $currentStreak++;
                if ($currentStreak >= $maxConsecutive) {
                    $maxConsecutive = $currentStreak;
                    $streakEndYear = $years[$i];
                }
            } else {
                $currentStreak = 1;
            }
        }

        if ($maxConsecutive >= $threshold) {
            $qualifyingRecord = $records->filter(fn (Record $r) => $r->caught?->year === $streakEndYear)->last() ?? $records->last();
            $awardedAt = $qualifyingRecord && $qualifyingRecord->caught ? $qualifyingRecord->caught : now();

            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord?->expedition,
                'awarded_at' => $awardedAt,
                'summary' => "Active across {$threshold} consecutive calendar years (through {$streakEndYear})",
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateDistinctLakes(Badge $badge, EloquentCollection $records, int $threshold): ?array
    {
        $seenLakes = [];
        $qualifyingRecord = null;

        foreach ($records as $record) {
            if ($record->lakes_id && !in_array($record->lakes_id, $seenLakes, true)) {
                $seenLakes[] = $record->lakes_id;
                if (count($seenLakes) === $threshold) {
                    $qualifyingRecord = $record;
                    break;
                }
            }
        }

        if ($qualifyingRecord !== null) {
            $lakeName = $qualifyingRecord->lake ? $qualifyingRecord->lake->name : 'Unknown Lake';

            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord->expedition,
                'awarded_at' => $qualifyingRecord->caught ?? now(),
                'summary' => "Explored lake #{$threshold}: {$lakeName}",
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateLakeSuperior(Badge $badge, EloquentCollection $records): ?array
    {
        $qualifyingRecord = $records->first(function (Record $r) {
            return $r->lake && str_contains(strtolower($r->lake->name), 'superior');
        });

        if ($qualifyingRecord !== null) {
            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord->expedition,
                'awarded_at' => $qualifyingRecord->caught ?? now(),
                'summary' => "Lake Superior open water catch: " . $this->formatRecordSummary($qualifyingRecord),
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateNewWater(Badge $badge, Angler $angler, EloquentCollection $records): ?array
    {
        foreach ($records as $record) {
            if (!$record->lakes_id) {
                continue;
            }

            // Check if this record is the earliest catch logged on this lake across all anglers
            $firstRecordOnLake = Record::where('lakes_id', $record->lakes_id)
                ->orderBy('caught', 'asc')
                ->first();

            if ($firstRecordOnLake && $firstRecordOnLake->id === $record->id) {
                $lakeName = $record->lake ? $record->lake->name : 'New Water';

                return [
                    'record' => $record,
                    'expedition' => $record->expedition,
                    'awarded_at' => $record->caught ?? now(),
                    'summary' => "First angler to map verified catch on {$lakeName}",
                ];
            }
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateWeatherPressureDrop(Badge $badge, EloquentCollection $records): ?array
    {
        // Search for pressure drop telemetry in daily weather or record context
        $qualifyingRecord = $records->first(function (Record $r) {
            if ($r->temperature && $r->temperature < 45.0) {
                return true;
            }
            if ($r->caught && $r->lake) {
                return $r->lake->dailyWeather()
                    ->where(function ($q) {
                        $q->where('pressure_trend', 'falling')
                          ->orWhere('window_pressure_delta', '<', -0.05);
                    })->exists();
            }
            return false;
        });

        if ($qualifyingRecord !== null) {
            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord->expedition,
                'awarded_at' => $qualifyingRecord->caught ?? now(),
                'summary' => "Catch logged during rapid barometer plunge: " . $this->formatRecordSummary($qualifyingRecord),
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateWeatherStorm(Badge $badge, EloquentCollection $records): ?array
    {
        // Checks for severe weather / low water temp / storm telemetry
        $qualifyingRecord = $records->first(function (Record $r) {
            if ($r->temperature && $r->temperature < 50.0) {
                return true;
            }
            if ($r->caught && $r->lake) {
                return $r->lake->dailyWeather()
                    ->where(function ($q) {
                        $q->whereIn('weather_condition', ['Rain', 'Storm', 'Thunderstorm', 'Heavy Rain'])
                          ->orWhere('wind_speed_max', '>', 20.0);
                    })->exists();
            }
            return false;
        });

        if ($qualifyingRecord !== null) {
            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord->expedition,
                'awarded_at' => $qualifyingRecord->caught ?? now(),
                'summary' => "Storm & rough water catch: " . $this->formatRecordSummary($qualifyingRecord),
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateFmzDistinct(Badge $badge, EloquentCollection $records, int $threshold): ?array
    {
        $seenFmzs = [];
        $qualifyingRecord = null;

        foreach ($records as $record) {
            $zoneId = $record->lake?->fishing_zone_id;
            if ($zoneId && !in_array($zoneId, $seenFmzs, true)) {
                $seenFmzs[] = $zoneId;
                if (count($seenFmzs) === $threshold) {
                    $qualifyingRecord = $record;
                    break;
                }
            }
        }

        if ($qualifyingRecord !== null) {
            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord->expedition,
                'awarded_at' => $qualifyingRecord->caught ?? now(),
                'summary' => "Catches across {$threshold} Fisheries Management Zones",
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateCumulativeInches(Badge $badge, EloquentCollection $records, float $threshold): ?array
    {
        $runningSum = 0.0;
        $qualifyingRecord = null;

        foreach ($records as $record) {
            $length = (float) ($record->length ?? 0.0);
            $runningSum += $length;

            if ($runningSum >= $threshold) {
                $qualifyingRecord = $record;
                break;
            }
        }

        if ($qualifyingRecord !== null) {
            $unitLabel = match (true) {
                $threshold >= 63360 => '1 mile',
                $threshold >= 3600 => round($threshold / 36) . ' yards',
                default => round($threshold / 12) . ' feet',
            };

            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord->expedition,
                'awarded_at' => $qualifyingRecord->caught ?? now(),
                'summary' => "Cumulative fish yardage crossed {$unitLabel} (" . number_format($threshold) . "\")",
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     */
    protected function evaluateReleasedCatches(Badge $badge, EloquentCollection $records, int $threshold): ?array
    {
        $releasedRecords = $records->filter(fn (Record $r) => (bool) $r->released)->values();

        if ($releasedRecords->count() >= $threshold) {
            $qualifyingRecord = $releasedRecords->get($threshold - 1);

            $awardedAt = $qualifyingRecord && $qualifyingRecord->caught ? $qualifyingRecord->caught : now();

            return [
                'record' => $qualifyingRecord,
                'expedition' => $qualifyingRecord?->expedition,
                'awarded_at' => $awardedAt,
                'summary' => "Verified release #{$threshold}: " . $this->formatRecordSummary($qualifyingRecord),
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Record> $records
     * @param EloquentCollection<int, Expedition> $expeditions
     */
    protected function evaluateSkunkedTrip(Badge $badge, EloquentCollection $records, EloquentCollection $expeditions): ?array
    {
        // Requires angler to have at least 1 successful expedition with catches, plus an expedition with 0 catches
        $expeditionsWithCatches = $records->pluck('trip_id')->filter()->unique();

        if ($expeditionsWithCatches->isEmpty()) {
            return null;
        }

        $skunkedExpedition = $expeditions->first(function (Expedition $expedition) use ($records) {
            $anglerCatchesOnTrip = $records->where('trip_id', $expedition->id)->count();
            return $anglerCatchesOnTrip === 0;
        });

        if ($skunkedExpedition !== null) {
            $title = $skunkedExpedition->title ?: 'Expedition';
            return [
                'record' => null,
                'expedition' => $skunkedExpedition,
                'awarded_at' => $skunkedExpedition->finish ?? $skunkedExpedition->start ?? now(),
                'summary' => "Skunked on {$title} — perseverance on the water",
            ];
        }

        return null;
    }

    /**
     * @param EloquentCollection<int, Expedition> $expeditions
     */
    protected function evaluateCrewRank(Badge $badge, Angler $angler, EloquentCollection $expeditions, int $rank): ?array
    {
        foreach ($expeditions as $expedition) {
            $crewAnglerIds = $expedition->crews->pluck('anglers_id')->unique();
            if ($crewAnglerIds->count() < 2) {
                continue;
            }

            // Tally catches per crew member on this expedition
            $leaderboard = $expedition->crews->map(function ($crew) use ($expedition) {
                $count = Record::where('anglers_id', $crew->anglers_id)
                    ->where('trip_id', $expedition->id)
                    ->count();
                return [
                    'angler_id' => $crew->anglers_id,
                    'count' => $count,
                ];
            })->sortByDesc('count')->values();

            if ($leaderboard->isEmpty()) {
                continue;
            }

            $rankedEntry = $leaderboard->get($rank - 1);
            if ($rankedEntry && $rankedEntry['angler_id'] === $angler->id && $rankedEntry['count'] > 0) {
                $title = $expedition->title ?: 'Expedition';
                $roleName = $rank === 1 ? 'Maverick (#1)' : 'Goose (#2)';

                return [
                    'record' => null,
                    'expedition' => $expedition,
                    'awarded_at' => $expedition->finish ?? now(),
                    'summary' => "{$roleName} Leaderboard finish on {$title} ({$rankedEntry['count']} catches)",
                ];
            }
        }

        return null;
    }

    /**
     * Format a concise record description string for trigger context.
     */
    protected function formatRecordSummary(?Record $record): string
    {
        if ($record === null) {
            return 'Verified Catch';
        }

        $length = $record->length ? "{$record->length}\"" : '';
        $breed = $record->fishBreed ? $record->fishBreed->name : 'Fish';
        $lake = $record->lake ? "at {$record->lake->name}" : '';

        return trim("{$length} {$breed} {$lake}");
    }

    /**
     * Persist newly awarded badge pivot record safely in a database transaction.
     *
     * @param array{record: Record|null, expedition: Expedition|null, awarded_at: Carbon, summary: string} $qualification
     */
    protected function awardBadge(Angler $angler, Badge $badge, array $qualification): AnglerBadge
    {
        return DB::transaction(function () use ($angler, $badge, $qualification) {
            return AnglerBadge::create([
                'anglers_id' => $angler->id,
                'badge_id' => $badge->id,
                'record_id' => $qualification['record']?->id,
                'expedition_id' => $qualification['expedition']?->id,
                'awarded_at' => $qualification['awarded_at'],
                'points' => $badge->points,
                'trigger_summary' => $qualification['summary'],
                'sync_status' => 'pending_upstream',
            ]);
        });
    }
}
