<?php

namespace Fishinglog\Services;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Record;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TrophyScoringService
{
    /**
     * Calculate species-normalized trophy score as a percentage of Master Angler benchmark length.
     *
     * @param Record|float $lengthOrRecord
     * @param FishBreed|null $breed
     * @return float
     */
    public function calculateScore(Record|float $lengthOrRecord, ?FishBreed $breed = null): float
    {
        if ($lengthOrRecord instanceof Record) {
            $length = (float) ($lengthOrRecord->length ?? 0);
            $breed = $lengthOrRecord->fishBreed;
        } else {
            $length = (float) $lengthOrRecord;
        }

        if ($length <= 0) {
            return 0.0;
        }

        $benchmark = $breed?->getBenchmarkLength() ?? 20.0;
        if ($benchmark <= 0) {
            $benchmark = 20.0;
        }

        return round(($length / $benchmark) * 100, 1);
    }

    /**
     * Get trophy rating tier metadata for a given normalized score.
     *
     * @param float $score
     * @return array{key: string, label: string, badge_variant: string, icon: string, is_trophy: bool}
     */
    public function getTrophyTier(float $score): array
    {
        if ($score >= 100.0) {
            return [
                'key' => 'master',
                'label' => 'Master Angler',
                'badge_variant' => 'amber',
                'icon' => 'trophy',
                'is_trophy' => true,
            ];
        }

        if ($score >= 90.0) {
            return [
                'key' => 'gold',
                'label' => 'Gold Class',
                'badge_variant' => 'emerald',
                'icon' => 'award',
                'is_trophy' => false,
            ];
        }

        if ($score >= 80.0) {
            return [
                'key' => 'silver',
                'label' => 'Silver Class',
                'badge_variant' => 'teal',
                'icon' => 'medal',
                'is_trophy' => false,
            ];
        }

        return [
            'key' => 'standard',
            'label' => 'Standard Catch',
            'badge_variant' => 'slate',
            'icon' => 'fish',
            'is_trophy' => false,
        ];
    }

    /**
     * Retrieve and rank top catches normalized across species with weight and length tie-breakers.
     *
     * @param Builder $query
     * @param int $limit
     * @return Collection<int, Record>
     */
    public function getTopNormalizedCatches(Builder $query, int $limit = 5): Collection
    {
        /** @var Collection<int, Record> $records */
        $records = (clone $query)
            ->with(['angler', 'fishBreed', 'lake', 'lure', 'photos'])
            ->whereNotNull('length')
            ->where('length', '>', 0)
            ->get();

        return $records->map(function (Record $record) {
            $record->setAttribute('trophy_score', $this->calculateScore($record));
            $record->setAttribute('trophy_tier', $this->getTrophyTier((float) $record->getAttribute('trophy_score')));
            return $record;
        })
        ->sort(function (Record $a, Record $b) {
            $scoreA = (float) $a->getAttribute('trophy_score');
            $scoreB = (float) $b->getAttribute('trophy_score');

            if ($scoreA !== $scoreB) {
                return $scoreB <=> $scoreA; // Descending
            }

            // Tie-breaker 1: Scale Weight
            $weightA = (float) ($a->weight ?? 0);
            $weightB = (float) ($b->weight ?? 0);
            if ($weightA !== $weightB) {
                return $weightB <=> $weightA;
            }

            // Tie-breaker 2: Length
            $lengthA = (float) ($a->length ?? 0);
            $lengthB = (float) ($b->length ?? 0);
            if ($lengthA !== $lengthB) {
                return $lengthB <=> $lengthA;
            }

            // Tie-breaker 3: Date caught
            return strcmp((string) $b->caught, (string) $a->caught);
        })
        ->values()
        ->take($limit);
    }

    /**
     * Estimate scale weight (lbs) from measured length (inches) using biological power curve (W = a * L^b).
     *
     * @param float $length
     * @param FishBreed|null $breed
     * @return float|null
     */
    public function estimateWeight(float $length, ?FishBreed $breed = null): ?float
    {
        if ($length <= 0) {
            return null;
        }

        $nameLower = strtolower(trim($breed->name ?? ''));

        // Standard length-weight regression coefficients for freshwater species (lbs vs inches)
        [$a, $b] = match (true) {
            str_contains($nameLower, 'smallmouth') => [0.00045, 3.13],
            str_contains($nameLower, 'largemouth') => [0.00039, 3.14],
            str_contains($nameLower, 'pike') => [0.00018, 3.05],
            str_contains($nameLower, 'muskellunge') || str_contains($nameLower, 'muskie') => [0.00015, 3.10],
            str_contains($nameLower, 'walleye') => [0.00027, 3.10],
            str_contains($nameLower, 'lake trout') => [0.00025, 3.12],
            str_contains($nameLower, 'brook trout') => [0.00040, 3.08],
            str_contains($nameLower, 'perch') => [0.00035, 3.15],
            str_contains($nameLower, 'crappie') => [0.00036, 3.12],
            default => [0.00030, 3.10],
        };

        $estimated = $a * pow($length, $b);
        return round($estimated, 2);
    }
}
