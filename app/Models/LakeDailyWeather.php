<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LakeDailyWeather extends Model
{
    use HasFactory;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $table = 'lake_daily_weather';

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'lakes_id',
        'date',
        'air_temp_max',
        'air_temp_min',
        'air_temp_mean',
        'barometric_pressure',
        'wind_speed_max',
        'wind_direction_dominant',
        'weather_condition',
        'weather_code',
        'hourly_telemetry',
        'window_pressure_start',
        'window_pressure_end',
        'window_pressure_delta',
        'pressure_trend',
    ];

    protected $casts = [
        'date' => 'date',
        'air_temp_max' => 'float',
        'air_temp_min' => 'float',
        'air_temp_mean' => 'float',
        'barometric_pressure' => 'float',
        'wind_speed_max' => 'float',
        'wind_direction_dominant' => 'integer',
        'weather_code' => 'integer',
        'hourly_telemetry' => 'array',
        'window_pressure_start' => 'float',
        'window_pressure_end' => 'float',
        'window_pressure_delta' => 'float',
    ];

    public function lake()
    {
        return $this->belongsTo(Lake::class, 'lakes_id', 'id');
    }

    /**
     * Helper to get telemetry for a specific hour (0 to 23).
     */
    public function getTelemetryForHour(int $hour): ?array
    {
        if (empty($this->hourly_telemetry) || !is_array($this->hourly_telemetry)) {
            return null;
        }

        foreach ($this->hourly_telemetry as $point) {
            if (isset($point['hour']) && (int) $point['hour'] === $hour) {
                return $point;
            }
        }

        return null;
    }

    /**
     * Helper to get cardinal wind direction string (e.g. N, ENE, SW).
     */
    public function getWindDirectionTextAttribute(): string
    {
        if (is_null($this->wind_direction_dominant)) {
            return 'N/A';
        }

        $deg = $this->wind_direction_dominant;
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $idx = round(($deg % 360) / 22.5) % 16;
        return $directions[$idx];
    }
}
