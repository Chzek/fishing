<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Lake;
use Fishinglog\Models\LakeDailyWeather;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherTelemetryService
{
    /**
     * WMO Weather Interpretation Codes (WW) mapping.
     */
    protected const WMO_CODES = [
        0 => 'Clear sky ☀️',
        1 => 'Mainly clear 🌤️',
        2 => 'Partly cloudy ⛅',
        3 => 'Overcast ☁️',
        45 => 'Fog 🌫️',
        48 => 'Depositing rime fog 🌫️',
        51 => 'Light drizzle 🌧️',
        53 => 'Moderate drizzle 🌧️',
        55 => 'Dense drizzle 🌧️',
        61 => 'Slight rain 🌧️',
        63 => 'Moderate rain 🌧️',
        65 => 'Heavy rain 🌧️',
        71 => 'Slight snow ❄️',
        73 => 'Moderate snow ❄️',
        75 => 'Heavy snow ❄️',
        80 => 'Slight rain showers 🌦️',
        81 => 'Moderate rain showers 🌦️',
        82 => 'Violent rain showers ⛈️',
        95 => 'Thunderstorm 🌩️',
        96 => 'Thunderstorm with slight hail ⛈️',
        99 => 'Thunderstorm with heavy hail ⛈️',
    ];

    /**
     * Fetch and store daily + hourly weather telemetry for a lake on a given date.
     *
     * @param Lake $lake
     * @param string $date YYYY-MM-DD
     * @param bool $force Force re-fetching Open-Meteo telemetry even if cached
     * @return LakeDailyWeather|null
     */
    public function fetchForLakeAndDate(Lake $lake, string $date, bool $force = false): ?LakeDailyWeather
    {
        // Return existing weather if already cached with hourly telemetry
        $existing = LakeDailyWeather::where('lakes_id', $lake->id)
            ->where('date', $date)
            ->first();

        if ($existing && !$force && !empty($existing->hourly_telemetry)) {
            return $existing;
        }

        // Must have coordinates
        if (is_null($lake->latitude) || is_null($lake->longitude)) {
            return null;
        }

        try {
            // Determine whether date is historical or current/forecast
            $isHistorical = strtotime($date) < strtotime(date('Y-m-d'));
            $endpoint = $isHistorical
                ? 'https://archive-api.open-meteo.com/v1/archive'
                : 'https://api.open-meteo.com/v1/forecast';

            $response = Http::timeout(5)->get($endpoint, [
                'latitude' => $lake->latitude,
                'longitude' => $lake->longitude,
                'start_date' => $date,
                'end_date' => $date,
                'daily' => 'temperature_2m_max,temperature_2m_min,temperature_2m_mean,surface_pressure_mean,wind_speed_10m_max,wind_direction_10m_dominant,weather_code',
                'hourly' => 'temperature_2m,surface_pressure,weather_code,wind_speed_10m',
                'temperature_unit' => 'fahrenheit',
                'wind_speed_unit' => 'mph',
                'timezone' => 'auto',
            ]);

            if (!$response->successful()) {
                Log::warning("Open-Meteo weather request failed for lake ID {$lake->id} on {$date}: HTTP {$response->status()}");
                return null;
            }

            $data = $response->json();
            $daily = $data['daily'] ?? null;
            $hourly = $data['hourly'] ?? null;

            if (empty($daily) || empty($daily['time'])) {
                return null;
            }

            $code = $daily['weather_code'][0] ?? null;
            $condition = self::WMO_CODES[$code] ?? 'Unknown 🌤️';

            // Process 24-hour Open-Meteo hourly telemetry array
            $hourlyPoints = [];
            if (!empty($hourly) && !empty($hourly['time'])) {
                $hTimes = $hourly['time'];
                $hTemps = $hourly['temperature_2m'] ?? [];
                $hPressures = $hourly['surface_pressure'] ?? [];
                $hCodes = $hourly['weather_code'] ?? [];
                $hWinds = $hourly['wind_speed_10m'] ?? [];

                for ($i = 0; $i < count($hTimes); $i++) {
                    $dtStr = $hTimes[$i] ?? null;
                    $hourNum = $dtStr ? (int) date('H', strtotime($dtStr)) : $i;
                    $hourlyPoints[] = [
                        'hour' => $hourNum,
                        'time' => $dtStr,
                        'temp' => isset($hTemps[$i]) ? (float) $hTemps[$i] : null,
                        'pressure' => isset($hPressures[$i]) ? (float) $hPressures[$i] : null,
                        'weather_code' => isset($hCodes[$i]) ? (int) $hCodes[$i] : null,
                        'wind_speed' => isset($hWinds[$i]) ? (float) $hWinds[$i] : null,
                    ];
                }
            }

            // Calculate Prime Evening Bite Window (4:00 PM / hour 16 to 9:00 PM / hour 21)
            $pStart = null;
            $pEnd = null;
            foreach ($hourlyPoints as $pt) {
                if ($pt['hour'] === 16) {
                    $pStart = $pt['pressure'];
                }
                if ($pt['hour'] === 21) {
                    $pEnd = $pt['pressure'];
                }
            }

            $pDelta = null;
            $pTrend = 'stable';
            if (!is_null($pStart) && !is_null($pEnd)) {
                $pDelta = round($pEnd - $pStart, 2);
                if ($pDelta <= -1.5) {
                    $pTrend = 'falling';
                } elseif ($pDelta >= 1.5) {
                    $pTrend = 'rising';
                } else {
                    $pTrend = 'stable';
                }
            }

            return LakeDailyWeather::updateOrCreate(
                [
                    'lakes_id' => $lake->id,
                    'date' => $date,
                ],
                [
                    'air_temp_max' => $daily['temperature_2m_max'][0] ?? null,
                    'air_temp_min' => $daily['temperature_2m_min'][0] ?? null,
                    'air_temp_mean' => $daily['temperature_2m_mean'][0] ?? null,
                    'barometric_pressure' => $daily['surface_pressure_mean'][0] ?? null,
                    'wind_speed_max' => $daily['wind_speed_10m_max'][0] ?? null,
                    'wind_direction_dominant' => $daily['wind_direction_10m_dominant'][0] ?? null,
                    'weather_code' => $code,
                    'weather_condition' => $condition,
                    'hourly_telemetry' => $hourlyPoints,
                    'window_pressure_start' => $pStart,
                    'window_pressure_end' => $pEnd,
                    'window_pressure_delta' => $pDelta,
                    'pressure_trend' => $pTrend,
                ]
            );
        } catch (\Throwable $e) {
            // Gracefully catch timeout / offline / connection errors
            Log::info("Unable to fetch weather telemetry (offline or unreachable API): {$e->getMessage()}");
            return null;
        }
    }
}
