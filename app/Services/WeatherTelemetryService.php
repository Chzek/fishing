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
     * @param string|\DateTimeInterface $date YYYY-MM-DD or DateTimeInterface
     * @param bool $force Force re-fetching Open-Meteo telemetry even if cached
     * @return LakeDailyWeather|null
     */
    public function fetchForLakeAndDate(Lake $lake, string|\DateTimeInterface $date, bool $force = false): ?LakeDailyWeather
    {
        $dateStr = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : substr(trim((string) $date), 0, 10);

        if (empty($dateStr) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return null;
        }

        // Return existing weather if already cached with hourly telemetry
        $existing = LakeDailyWeather::where('lakes_id', $lake->id)
            ->where('date', $dateStr)
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
            $isHistorical = strtotime($dateStr) < strtotime(date('Y-m-d'));
            $endpoint = $isHistorical
                ? 'https://archive-api.open-meteo.com/v1/archive'
                : 'https://api.open-meteo.com/v1/forecast';

            $response = Http::timeout(5)->get($endpoint, [
                'latitude' => $lake->latitude,
                'longitude' => $lake->longitude,
                'start_date' => $dateStr,
                'end_date' => $dateStr,
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
                    'date' => $dateStr,
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

    /**
     * Compute 3-hour barometric pressure velocity and 5-category tactical angling intelligence.
     *
     * @param array<int, array<string, mixed>>|null $hourlyPoints
     * @param float|null $fallbackDelta
     * @param string|null $fallbackTrend
     * @param int|null $targetHour
     * @return array{
     *     velocity_3h: float|null,
     *     classification: string,
     *     badge_label: string,
     *     tactical_advice: string,
     *     target_species_depth: string,
     *     lure_recommendations: string,
     *     badge_class: string,
     *     border_class: string,
     *     text_class: string,
     *     icon: string,
     *     icon_color: string
     * }
     */
    public function calculatePressureVelocity(
        ?array $hourlyPoints = null,
        ?float $fallbackDelta = null,
        ?string $fallbackTrend = null,
        ?int $targetHour = null
    ): array {
        $velocity = null;

        if (!empty($hourlyPoints)) {
            $pointsByHour = [];
            foreach ($hourlyPoints as $pt) {
                if (isset($pt['hour']) && isset($pt['pressure'])) {
                    $pointsByHour[(int) $pt['hour']] = (float) $pt['pressure'];
                }
            }

            if (!empty($pointsByHour)) {
                if (!is_null($targetHour)) {
                    $currHour = max(0, min(23, $targetHour));
                    $prevHour = max(0, $currHour - 3);
                    if (isset($pointsByHour[$currHour]) && isset($pointsByHour[$prevHour])) {
                        $velocity = round($pointsByHour[$currHour] - $pointsByHour[$prevHour], 2);
                    }
                }

                // If velocity still null, calculate from prime evening window (hour 21 vs hour 18 or 16)
                if (is_null($velocity)) {
                    if (isset($pointsByHour[21]) && isset($pointsByHour[18])) {
                        $velocity = round($pointsByHour[21] - $pointsByHour[18], 2);
                    } elseif (isset($pointsByHour[21]) && isset($pointsByHour[16])) {
                        // 5h window normalized to 3h: delta * 0.6
                        $velocity = round(($pointsByHour[21] - $pointsByHour[16]) * (3.0 / 5.0), 2);
                    }
                }

                // If still null, try latest consecutive 3h delta available
                if (is_null($velocity)) {
                    $hours = array_keys($pointsByHour);
                    sort($hours);
                    $maxH = (int) end($hours);
                    $minH = max(0, $maxH - 3);
                    if (isset($pointsByHour[$maxH]) && isset($pointsByHour[$minH]) && $maxH > $minH) {
                        $velocity = round(($pointsByHour[$maxH] - $pointsByHour[$minH]) * (3.0 / ($maxH - $minH)), 2);
                    }
                }
            }
        }

        // Fallback to stored window_pressure_delta if available
        if (is_null($velocity) && !is_null($fallbackDelta)) {
            $velocity = round($fallbackDelta * 0.6, 2);
        }

        // Classify into 5 distinct angling states
        if (!is_null($velocity)) {
            if ($velocity <= -2.0) {
                $classification = 'rapid_drop';
            } elseif ($velocity <= -0.8) {
                $classification = 'falling';
            } elseif ($velocity >= 2.0) {
                $classification = 'rapid_rise';
            } elseif ($velocity >= 0.8) {
                $classification = 'rising';
            } else {
                $classification = 'stable';
            }
        } elseif ($fallbackTrend === 'falling') {
            $classification = 'falling';
            $velocity = -1.2;
        } elseif ($fallbackTrend === 'rising') {
            $classification = 'rising';
            $velocity = 1.2;
        } else {
            $classification = 'stable';
            $velocity = 0.0;
        }

        $configs = [
            'rapid_drop' => [
                'badge_label' => 'Rapid Drop: Pre-Frontal Surge (' . $velocity . ' hPa/3h)',
                'tactical_advice' => 'Fish are aggressively feeding before the incoming storm front. Target shallow flats and weedlines with fast-moving reaction baits.',
                'target_species_depth' => 'Shallow 2–8 ft / Active surface & upper water column',
                'lure_recommendations' => 'Topwater frogs, buzzbaits, chatterbaits, lipless crankbaits & fast spinnerbaits',
                'badge_class' => 'bg-emerald-50 text-emerald-900 border-emerald-300 shadow-sm',
                'border_class' => 'border-emerald-300',
                'text_class' => 'text-emerald-800',
                'icon' => 'zap',
                'icon_color' => 'text-emerald-600',
            ],
            'falling' => [
                'badge_label' => 'Falling Barometer: Active Feeding Window (' . $velocity . ' hPa/3h)',
                'tactical_advice' => 'Barometer is dropping steadily. Fish are active, moving out of heavy cover and roaming structure to feed.',
                'target_species_depth' => 'Mid-depth 6–15 ft / Structure edges & weed edges',
                'lure_recommendations' => 'Jerkbaits, medium-diving crankbaits, swimbaits & willow-blade spinnerbaits',
                'badge_class' => 'bg-emerald-50/80 text-emerald-800 border-emerald-200',
                'border_class' => 'border-emerald-200',
                'text_class' => 'text-emerald-700',
                'icon' => 'trending-down',
                'icon_color' => 'text-emerald-600',
            ],
            'stable' => [
                'badge_label' => 'Stable Barometer: Consistent Depth Patterns (' . ($velocity >= 0 ? '+' : '') . $velocity . ' hPa/3h)',
                'tactical_advice' => 'Consistent atmospheric pressure. Fish are holding in standard seasonal holding areas; rely on proven contour structure patterns.',
                'target_species_depth' => 'Seasonal holding depth / Main lake humps, points & drop-offs',
                'lure_recommendations' => 'Football jigs, Texas-rigged worms, tube jigs & deep crankbaits',
                'badge_class' => 'bg-sky-50 text-sky-800 border-sky-200',
                'border_class' => 'border-sky-200',
                'text_class' => 'text-sky-700',
                'icon' => 'minus',
                'icon_color' => 'text-sky-600',
            ],
            'rising' => [
                'badge_label' => 'Rising Barometer: Fish Tight to Cover (' . '+' . $velocity . ' hPa/3h)',
                'tactical_advice' => 'Barometer is rising following weather shift. Fish are holding tighter to wood, dense weeds, and deep shade. Slow down cadence.',
                'target_species_depth' => 'Tight to heavy cover / Wood, docks & dense vegetation',
                'lure_recommendations' => 'Pitching jigs, wacky worms, slow-fall stickbaits & weighted tubes',
                'badge_class' => 'bg-amber-50 text-amber-800 border-amber-200',
                'border_class' => 'border-amber-200',
                'text_class' => 'text-amber-700',
                'icon' => 'trending-up',
                'icon_color' => 'text-amber-600',
            ],
            'rapid_rise' => [
                'badge_label' => 'Post-Front High Pressure: Finesse Required (' . '+' . $velocity . ' hPa/3h)',
                'tactical_advice' => 'Bluebird high pressure conditions with lockjaw bite. Fish are glued to bottom or deep structure. Extreme finesse presentations required.',
                'target_species_depth' => 'Deep bottom structure 18–35+ ft / Basins & steep drop-offs',
                'lure_recommendations' => 'Downsized Ned rigs, drop-shot finesse worms, hair jigs & live bait rigs',
                'badge_class' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                'border_class' => 'border-indigo-200',
                'text_class' => 'text-indigo-700',
                'icon' => 'shield-alert',
                'icon_color' => 'text-indigo-600',
            ],
        ];

        $res = $configs[$classification];
        $res['velocity_3h'] = $velocity;
        $res['classification'] = $classification;

        return $res;
    }
}
