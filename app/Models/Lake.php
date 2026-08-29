<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lake extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'name',
        'latitude',
        'longitude',
        'structure',
        'max_depth',
        'fishing_zone_id',
    ];

    public function fishingZone()
    {
        return $this->belongsTo(FishingZone::class, 'fishing_zone_id', 'id');
    }

    public function rules()
    {
        return $this->hasMany(FishingRule::class, 'lake_id', 'id');
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'lakes_id', 'id');
    }

    public function anglers()
    {
        return $this->hasManyThrough(Angler::class, Record::class, 'lakes_id', 'id', 'id', 'anglers_id');
    }

    public function dailyWeather()
    {
        return $this->hasMany(LakeDailyWeather::class, 'lakes_id', 'id');
    }

    public function getDailyWeatherForDate($date)
    {
        return $this->dailyWeather()->where('date', $date)->first();
    }

    /**
     * Scope query to only include lakes with valid GPS coordinates.
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude')->where('latitude', '!=', 0)->where('longitude', '!=', 0);
    }

    /**
     * Biggest fish by length of the lake.
     * 
     * @return \Fishinglog\Models\Record|null
     */
    public function biggestCatch()
    {
        return $this->records()
            ->orderBy('records.length', 'desc')
            ->first();
    }

    /**
     * Find nearby lakes within a radius in miles.
     *
     * @param float $lat
     * @param float $lng
     * @param float $radiusMiles
     * @param int|null $excludeId
     * @return \Illuminate\Support\Collection
     */
    public static function nearby($lat, $lng, $radiusMiles = 2.0, $excludeId = null)
    {
        if (is_null($lat) || is_null($lng) || $lat == 0 || $lng == 0) {
            return collect([]);
        }

        $latDelta = $radiusMiles / 69.0;
        $cosLat = cos(deg2rad($lat));
        $lngDelta = $radiusMiles / (69.0 * ($cosLat == 0 ? 1 : abs($cosLat)));

        $query = static::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $lakes = $query->get();

        return $lakes->map(function ($lake) use ($lat, $lng) {
            $lake->distance = static::haversineDistance($lat, $lng, $lake->latitude, $lake->longitude);
            return $lake;
        })->filter(function ($lake) use ($radiusMiles) {
            return $lake->distance <= $radiusMiles;
        })->sortBy('distance')->values();
    }

    /**
     * Calculate Haversine distance in miles between two coordinates.
     */
    public static function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 3958.8; // Miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
