<?php

namespace Fishinglog\Services;

use Fishinglog\Models\FishingZone;

class GeoZoneDetector
{
    private static ?array $geoJsonData = null;

    /**
     * Detect matching FishingZone by lat/lng coordinates.
     *
     * @param float $lat
     * @param float $lng
     * @return \Fishinglog\Models\FishingZone|null
     */
    public static function detectZone($lat, $lng): ?FishingZone
    {
        if (!$lat || !$lng) {
            return null;
        }

        $code = static::detectZoneCode($lat, $lng);
        if (!$code) {
            return null;
        }

        return FishingZone::where('code', $code)->first();
    }

    /**
     * Detect matching zone code (e.g. "FMZ 7") by lat/lng coordinates.
     *
     * @param float $lat
     * @param float $lng
     * @return string|null
     */
    public static function detectZoneCode($lat, $lng): ?string
    {
        $features = static::getGeoJsonFeatures();
        if (empty($features)) {
            return null;
        }

        foreach ($features as $feature) {
            $code = $feature['properties']['code'] ?? null;
            if (!$code) continue;

            $geometry = $feature['geometry'] ?? null;
            if (!$geometry) continue;

            $type = $geometry['type'] ?? '';
            $coordinates = $geometry['coordinates'] ?? [];

            if ($type === 'Polygon') {
                foreach ($coordinates as $ring) {
                    if (static::pointInPolygon($lat, $lng, $ring)) {
                        return $code;
                    }
                }
            } elseif ($type === 'MultiPolygon') {
                foreach ($coordinates as $polygon) {
                    foreach ($polygon as $ring) {
                        if (static::pointInPolygon($lat, $lng, $ring)) {
                            return $code;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Ray-casting Point-in-Polygon algorithm.
     *
     * @param float $lat
     * @param float $lng
     * @param array $ring Array of [longitude, latitude] vertices
     * @return bool
     */
    private static function pointInPolygon($lat, $lng, array $ring): bool
    {
        $inside = false;
        $numPoints = count($ring);

        for ($i = 0, $j = $numPoints - 1; $i < $numPoints; $j = $i++) {
            $xi = $ring[$i][0]; // Lng
            $yi = $ring[$i][1]; // Lat
            $xj = $ring[$j][0];
            $yj = $ring[$j][1];

            $intersect = (($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / (($yj - $yi) ?: 0.0000000001) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /**
     * Load cached GeoJSON features.
     */
    private static function getGeoJsonFeatures(): array
    {
        if (self::$geoJsonData !== null) {
            return self::$geoJsonData;
        }

        $filePath = public_path('json/ontario-fmz-boundaries-web.geojson');
        if (!file_exists($filePath)) {
            self::$geoJsonData = [];
            return [];
        }

        $content = file_get_contents($filePath);
        $json = json_decode($content, true);
        self::$geoJsonData = $json['features'] ?? [];

        return self::$geoJsonData;
    }
}
