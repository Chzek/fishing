<?php

namespace Fishinglog\Actions\Lakes;

use Fishinglog\Models\Lake;
use Fishinglog\Services\GeoZoneDetector;
use Illuminate\Support\Facades\DB;

class UpdateLakeAction
{
    /**
     * Execute the action to update an existing lake with automated GIS fishing zone detection.
     *
     * @param Lake $lake
     * @param array<string, mixed> $data
     * @return Lake
     */
    public function execute(Lake $lake, array $data): Lake
    {
        return DB::transaction(function () use ($lake, $data): Lake {
            $lake->name = (string) ($data['name'] ?? $lake->name);
            $lake->latitude = isset($data['latitude']) && $data['latitude'] !== '' ? (float) $data['latitude'] : null;
            $lake->longitude = isset($data['longitude']) && $data['longitude'] !== '' ? (float) $data['longitude'] : null;
            $lake->structure = isset($data['structure']) && $data['structure'] !== '' ? (string) $data['structure'] : null;
            $lake->max_depth = isset($data['max_depth']) && $data['max_depth'] !== '' ? (float) $data['max_depth'] : null;

            if (!empty($data['fishing_zone_id'])) {
                $lake->fishing_zone_id = (string) $data['fishing_zone_id'];
            } elseif ($lake->latitude !== null && $lake->longitude !== null) {
                $detectedZone = GeoZoneDetector::detectZone($lake->latitude, $lake->longitude);
                if ($detectedZone) {
                    $lake->fishing_zone_id = $detectedZone->id;
                }
            }

            $lake->save();

            return $lake;
        });
    }
}
