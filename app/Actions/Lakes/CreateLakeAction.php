<?php

namespace Fishinglog\Actions\Lakes;

use Fishinglog\Models\Lake;
use Fishinglog\Services\GeoZoneDetector;
use Illuminate\Support\Facades\DB;

class CreateLakeAction
{
    /**
     * Execute the action to create a new lake with automated GIS fishing zone detection.
     *
     * @param array<string, mixed> $data
     * @return Lake
     */
    public function execute(array $data): Lake
    {
        return DB::transaction(function () use ($data): Lake {
            $lake = new Lake();
            $lake->name = (string) ($data['name'] ?? '');
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
