<?php

namespace Fishinglog\Services;

use DateTimeInterface;
use Illuminate\Support\Carbon;

class SolunarService
{
    /**
     * Astronomical synodic lunar month length in days.
     */
    public const SYNODIC_MONTH = 29.53058867;

    /**
     * Known reference New Moon Julian Date (Jan 6, 2000, 18:14 UTC).
     */
    public const REFERENCE_NEW_MOON_JD = 2451549.5;

    /**
     * Compute full offline Solunar forecast for given GPS coordinates and date.
     *
     * @param float $latitude
     * @param float $longitude
     * @param string|DateTimeInterface $date YYYY-MM-DD
     * @return array
     */
    public function getSolunarData(float $latitude, float $longitude, string|DateTimeInterface $date): array
    {
        $carbonDate = is_string($date) ? Carbon::parse($date) : Carbon::instance($date);
        $year = (int) $carbonDate->format('Y');
        $month = (int) $carbonDate->format('m');
        $day = (int) $carbonDate->format('d');

        $jd = $this->calculateJulianDate($year, $month, $day);

        // Lunar Phase & Illumination
        $daysSinceNew = fmod($jd - self::REFERENCE_NEW_MOON_JD, self::SYNODIC_MONTH);
        if ($daysSinceNew < 0) {
            $daysSinceNew += self::SYNODIC_MONTH;
        }

        $phaseAngle = ($daysSinceNew / self::SYNODIC_MONTH) * 2 * M_PI;
        $illuminationPct = (int) round(((1 - cos($phaseAngle)) / 2) * 100);
        $phaseInfo = $this->getPhaseInfo($daysSinceNew);

        // Sun Rise & Set approximation
        $sunTimes = $this->calculateSunTimes($latitude, $longitude, $carbonDate);

        // Moon Transits (Overhead & Underfoot) & Rise/Set
        // At New Moon (day 0), Moon transits overhead at ~12:00 noon. At Full Moon (day 14.76), at ~00:00 midnight.
        $overheadHour = fmod(12.0 + ($daysSinceNew * (24.0 / self::SYNODIC_MONTH)), 24.0);
        $underfootHour = fmod($overheadHour + 12.0, 24.0);
        $moonriseHour = fmod($overheadHour - 6.2 + 24.0, 24.0);
        $moonsetHour = fmod($overheadHour + 6.2, 24.0);

        // Major Windows (2 hours each, centered on transits)
        $major1 = $this->formatWindow($overheadHour, 2.0, 'Overhead Transit');
        $major2 = $this->formatWindow($underfootHour, 2.0, 'Underfoot Transit');

        // Minor Windows (1 hour each, centered on rise/set)
        $minor1 = $this->formatWindow($moonriseHour, 1.0, 'Moonrise');
        $minor2 = $this->formatWindow($moonsetHour, 1.0, 'Moonset');

        // Day Quality Rating (1 to 5 Stars)
        $rating = $this->calculateDayRating($daysSinceNew, $illuminationPct);

        // 24-hour timeline bar data (00:00 to 23:59)
        $hourlyIntensity = $this->generateHourlyIntensity(
            $overheadHour,
            $underfootHour,
            $moonriseHour,
            $moonsetHour,
            $sunTimes['sunriseHour'],
            $sunTimes['sunsetHour']
        );

        // Smooth 24-hour continuous wave curve data for visual chart
        $waveCurve = $this->generateWaveCurve(
            $overheadHour,
            $underfootHour,
            $moonriseHour,
            $moonsetHour,
            300,
            38,
            38
        );

        // SVG Moon Phase Path
        $moonSvg = $this->generateMoonSvgPath($daysSinceNew);

        return [
            'date' => $carbonDate->format('Y-m-d'),
            'formattedDate' => $carbonDate->format('l, M j, Y'),
            'coordinates' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ],
            'moon' => [
                'phase' => $phaseInfo['name'],
                'emoji' => $phaseInfo['emoji'],
                'ageDays' => round($daysSinceNew, 1),
                'illumination' => $illuminationPct,
                'svgPath' => $moonSvg,
            ],
            'rating' => $rating,
            'sun' => [
                'sunrise' => $sunTimes['sunrise'],
                'sunset' => $sunTimes['sunset'],
                'dayLength' => $sunTimes['dayLength'],
            ],
            'majorWindows' => [
                $major1,
                $major2,
            ],
            'minorWindows' => [
                $minor1,
                $minor2,
            ],
            'hourlyIntensity' => $hourlyIntensity,
            'waveCurve' => $waveCurve,
        ];
    }

    /**
     * Calculate Julian Date Number at 00:00 UTC.
     */
    public function calculateJulianDate(int $year, int $month, int $day): float
    {
        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }

        $a = floor($year / 100);
        $b = 2 - $a + floor($a / 4);

        return floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524.5;
    }

    /**
     * Determine Moon Phase Name & Icon from Lunar Age in Days.
     */
    protected function getPhaseInfo(float $ageDays): array
    {
        if ($ageDays < 1.84 || $ageDays >= 27.69) {
            return ['name' => 'New Moon', 'emoji' => '🌑'];
        }
        if ($ageDays < 5.53) {
            return ['name' => 'Waxing Crescent', 'emoji' => '🌒'];
        }
        if ($ageDays < 9.22) {
            return ['name' => 'First Quarter', 'emoji' => '🌓'];
        }
        if ($ageDays < 12.91) {
            return ['name' => 'Waxing Gibbous', 'emoji' => '🌔'];
        }
        if ($ageDays < 16.61) {
            return ['name' => 'Full Moon', 'emoji' => '🌕'];
        }
        if ($ageDays < 20.30) {
            return ['name' => 'Waning Gibbous', 'emoji' => '🌖'];
        }
        if ($ageDays < 23.99) {
            return ['name' => 'Last Quarter', 'emoji' => '🌗'];
        }
        return ['name' => 'Waning Crescent', 'emoji' => '🌘'];
    }

    /**
     * Calculate approximate Sunrise and Sunset times based on latitude and day of year.
     */
    protected function calculateSunTimes(float $latitude, float $longitude, Carbon $date): array
    {
        $dayOfYear = (int) $date->format('z') + 1;
        
        // Solar declination approximation in radians
        $declination = 0.4095 * sin(2 * M_PI * ($dayOfYear - 79.345) / 365.0);
        
        // Latitude in radians
        $latRad = deg2rad($latitude);
        
        // Hour angle calculation
        $cosHourAngle = -tan($latRad) * tan($declination);
        
        // Clamp for polar regions
        $cosHourAngle = max(-1.0, min(1.0, $cosHourAngle));
        $hourAngle = acos($cosHourAngle) * (12.0 / M_PI);
        
        // Approximate solar noon around 12:30 local daylight time in typical mid-Ontario zones
        $solarNoon = 12.5; 
        
        $sunriseHour = max(4.0, min(8.0, $solarNoon - $hourAngle));
        $sunsetHour = max(16.0, min(22.0, $solarNoon + $hourAngle));
        $dayLengthHours = $sunsetHour - $sunriseHour;

        return [
            'sunrise' => $this->decimalHoursToTime($sunriseHour),
            'sunset' => $this->decimalHoursToTime($sunsetHour),
            'sunriseHour' => $sunriseHour,
            'sunsetHour' => $sunsetHour,
            'dayLength' => sprintf('%dh %02dm', floor($dayLengthHours), round(($dayLengthHours - floor($dayLengthHours)) * 60)),
        ];
    }

    /**
     * Format a feeding time window given center decimal hour and duration.
     */
    protected function formatWindow(float $centerHour, float $durationHours, string $type): array
    {
        $half = $durationHours / 2.0;
        $startHour = fmod($centerHour - $half + 24.0, 24.0);
        $endHour = fmod($centerHour + $half + 24.0, 24.0);

        return [
            'type' => $type,
            'duration' => $durationHours >= 2.0 ? '2 Hours' : '1 Hour',
            'start' => $this->decimalHoursToTime($startHour),
            'end' => $this->decimalHoursToTime($endHour),
            'startHour' => round($startHour, 2),
            'endHour' => round($endHour, 2),
            'display' => sprintf('%s &ndash; %s', $this->decimalHoursToTime($startHour), $this->decimalHoursToTime($endHour)),
        ];
    }

    /**
     * Compute overall day quality score (1 to 5 Stars).
     */
    protected function calculateDayRating(float $ageDays, int $illuminationPct): array
    {
        // Full moon (day 14-16) and New moon (day 28-1.5) offer highest biological feeding triggers
        $distFromNew = min($ageDays, abs(self::SYNODIC_MONTH - $ageDays));
        $distFromFull = abs($ageDays - 14.76);
        $minDist = min($distFromNew, $distFromFull);

        if ($minDist <= 1.5) {
            return [
                'score' => 5,
                'label' => 'EXCELLENT',
                'stars' => '⭐⭐⭐⭐⭐',
                'color' => 'emerald',
                'description' => 'Peak peak bite intensity. Strong tidal and feeding triggers active.',
            ];
        }

        if ($minDist <= 3.5) {
            return [
                'score' => 4,
                'label' => 'VERY GOOD',
                'stars' => '⭐⭐⭐⭐',
                'color' => 'teal',
                'description' => 'Above average fish activity during Major transit windows.',
            ];
        }

        if ($minDist <= 5.5) {
            return [
                'score' => 3,
                'label' => 'AVERAGE',
                'stars' => '⭐⭐⭐',
                'color' => 'amber',
                'description' => 'Moderate bite activity. Target Major overhead/underfoot periods.',
            ];
        }

        return [
            'score' => 2,
            'label' => 'FAIR',
            'stars' => '⭐⭐',
            'color' => 'slate',
            'description' => 'Slower bite windows. Focus strictly on dawn/dusk feeding overlaps.',
        ];
    }

    /**
     * Generate 24-hour activity array for the visual timeline strip.
     */
    protected function generateHourlyIntensity(
        float $overhead,
        float $underfoot,
        float $moonrise,
        float $moonset,
        float $sunrise,
        float $sunset
    ): array {
        $timeline = [];

        for ($h = 0; $h < 24; $h++) {
            $isMajor1 = $this->isHourInWindow($h, $overhead, 2.0);
            $isMajor2 = $this->isHourInWindow($h, $underfoot, 2.0);
            $isMinor1 = $this->isHourInWindow($h, $moonrise, 1.0);
            $isMinor2 = $this->isHourInWindow($h, $moonset, 1.0);
            $isSunPeriod = ($h >= floor($sunrise) && $h <= ceil($sunrise)) || ($h >= floor($sunset) && $h <= ceil($sunset));

            $status = 'normal';
            $level = 1;

            if ($isMajor1 || $isMajor2) {
                $status = 'major';
                $level = ($isSunPeriod) ? 4 : 3; // Golden overlap
            } elseif ($isMinor1 || $isMinor2) {
                $status = 'minor';
                $level = ($isSunPeriod) ? 3 : 2;
            }

            $timeline[] = [
                'hour' => $h,
                'timeLabel' => sprintf('%02d:00', $h),
                'status' => $status,
                'level' => $level,
                'isDaylight' => ($h >= $sunrise && $h < $sunset),
                'isSunrise' => (int) round($sunrise) === $h,
                'isSunset' => (int) round($sunset) === $h,
            ];
        }

        return $timeline;
    }

    /**
     * Check if a given whole hour falls within a center window.
     */
    protected function isHourInWindow(int $hour, float $centerHour, float $duration): bool
    {
        $half = $duration / 2.0;
        $start = fmod($centerHour - $half + 24.0, 24.0);
        $end = fmod($centerHour + $half + 24.0, 24.0);

        if ($start <= $end) {
            return ($hour >= floor($start) && $hour <= ceil($end));
        }

        // Wraps over midnight
        return ($hour >= floor($start) || $hour <= ceil($end));
    }

    /**
     * Convert decimal hour (e.g. 14.75) to 12-hour AM/PM string (e.g. '02:45 PM').
     */
    protected function decimalHoursToTime(float $decimalHour): string
    {
        $normalized = fmod($decimalHour + 24.0, 24.0);
        $hours = (int) floor($normalized);
        $minutes = (int) round(($normalized - $hours) * 60);

        if ($minutes === 60) {
            $hours = ($hours + 1) % 24;
            $minutes = 0;
        }

        $period = $hours >= 12 ? 'PM' : 'AM';
        $displayHour = $hours % 12;
        if ($displayHour === 0) {
            $displayHour = 12;
        }

        return sprintf('%02d:%02d %s', $displayHour, $minutes, $period);
    }

    /**
     * Generate SVG path for the illuminated moon phase.
     */
    public function generateMoonSvgPath(float $ageDays): array
    {
        $r = 14;
        $cx = 16;
        $cy = 16;
        $top = $cy - $r;
        $bottom = $cy + $r;

        $p = fmod($ageDays, self::SYNODIC_MONTH) / self::SYNODIC_MONTH;
        $angle = $p * 2 * M_PI;
        $cosA = cos($angle);
        $rx = abs($r * $cosA);

        $isWaxing = ($p <= 0.5);
        $isCrescent = ($cosA > 0);

        if ($p < 0.02 || $p > 0.98) {
            return [
                'type' => 'new',
                'path' => '',
                'fill' => '#1e293b',
            ];
        }

        if ($p >= 0.48 && $p <= 0.52) {
            return [
                'type' => 'full',
                'path' => "M {$cx} {$top} A {$r} {$r} 0 1 1 {$cx} {$bottom} A {$r} {$r} 0 1 1 {$cx} {$top}",
                'fill' => '#e2e8f0',
            ];
        }

        if ($isWaxing) {
            $outer = "M {$cx} {$top} A {$r} {$r} 0 0 1 {$cx} {$bottom}";
            $sweep = $isCrescent ? 0 : 1;
            $inner = "A {$rx} {$r} 0 0 {$sweep} {$cx} {$top} Z";
        } else {
            $outer = "M {$cx} {$top} A {$r} {$r} 0 0 0 {$cx} {$bottom}";
            $sweep = $isCrescent ? 1 : 0;
            $inner = "A {$rx} {$r} 0 0 {$sweep} {$cx} {$top} Z";
        }

        return [
            'type' => 'phase',
            'path' => "{$outer} {$inner}",
            'fill' => '#e2e8f0',
        ];
    }

    /**
     * Generate smooth 24-hour continuous wave curve data for visual chart.
     */
    public function generateWaveCurve(
        float $overhead,
        float $underfoot,
        float $moonrise,
        float $moonset,
        int $width = 300,
        int $height = 36,
        int $baseY = 36
    ): array {
        $numSamples = 120;
        $stepX = $width / $numSamples;
        $majors = [$overhead, $underfoot];
        $minors = [$moonrise, $moonset];

        $points = [];
        for ($i = 0; $i <= $numSamples; $i++) {
            $t = ($i / $numSamples) * 24.0;
            $x = round($i * $stepX, 2);

            $intensity = 0.0;

            foreach ($majors as $center) {
                $diff = abs($t - $center);
                if ($diff > 12.0) {
                    $diff = 24.0 - $diff;
                }
                $intensity += 29.0 * exp(- ($diff * $diff) / (2 * 1.35 * 1.35));
            }

            foreach ($minors as $center) {
                $diff = abs($t - $center);
                if ($diff > 12.0) {
                    $diff = 24.0 - $diff;
                }
                $intensity += 15.0 * exp(- ($diff * $diff) / (2 * 0.95 * 0.95));
            }

            $curveHeight = min($height - 2, max(0.5, $intensity));
            $y = round($baseY - $curveHeight, 2);

            $points[] = ['x' => $x, 'y' => $y, 't' => $t, 'intensity' => $intensity];
        }

        $first = $points[0];
        $strokeParts = ["M {$first['x']} {$first['y']}"];
        for ($i = 1; $i < count($points); $i++) {
            $strokeParts[] = "L {$points[$i]['x']} {$points[$i]['y']}";
        }
        $strokePath = implode(' ', $strokeParts);

        $last = end($points);
        $fillPath = "M 0 {$baseY} L {$first['x']} {$first['y']} " . implode(' ', array_slice($strokeParts, 1)) . " L {$last['x']} {$baseY} Z";

        return [
            'fillPath' => $fillPath,
            'strokePath' => $strokePath,
            'viewBox' => "0 0 {$width} {$baseY}",
            'width' => $width,
            'height' => $height,
            'baseY' => $baseY,
        ];
    }
}
