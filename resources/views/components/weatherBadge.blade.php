@props([
    'weather' => null,
    'size' => 'md',
    'showEmoji' => false,
    'showTrend' => true,
    'compact' => false,
])

@php
    if (!$weather) {
        return;
    }

    $airTemp = is_object($weather) ? ($weather->air_temp_mean ?? $weather->air_temp ?? null) : ($weather['air_temp_mean'] ?? $weather['air_temp'] ?? null);
    $pressure = is_object($weather) ? ($weather->barometric_pressure ?? null) : ($weather['barometric_pressure'] ?? null);
    $condition = is_object($weather) ? ($weather->weather_condition ?? null) : ($weather['weather_condition'] ?? null);
    $trend = is_object($weather) ? ($weather->pressure_trend ?? null) : ($weather['pressure_trend'] ?? null);
    $delta = is_object($weather) ? ($weather->window_pressure_delta ?? null) : ($weather['window_pressure_delta'] ?? null);

    $cleanCondition = trim(preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', (string) $condition));
    $condLower = strtolower((string) $condition);

    $config = match (true) {
        str_contains($condLower, 'clear sky') || $condLower === 'sunny' || $condLower === 'clear' => [
            'icon' => 'sun',
            'emoji' => '☀️',
            'color' => 'text-amber-500',
        ],
        str_contains($condLower, 'mainly clear') => [
            'icon' => 'sun-medium',
            'emoji' => '🌤️',
            'color' => 'text-amber-400',
        ],
        str_contains($condLower, 'partly') || str_contains($condLower, 'cloudy') => [
            'icon' => 'cloud-sun',
            'emoji' => '⛅',
            'color' => 'text-amber-400',
        ],
        str_contains($condLower, 'overcast') => [
            'icon' => 'cloud',
            'emoji' => '☁️',
            'color' => 'text-slate-400',
        ],
        str_contains($condLower, 'fog') => [
            'icon' => 'cloud-fog',
            'emoji' => '🌫️',
            'color' => 'text-slate-400',
        ],
        str_contains($condLower, 'drizzle') => [
            'icon' => 'cloud-drizzle',
            'emoji' => '🌧️',
            'color' => 'text-sky-400',
        ],
        str_contains($condLower, 'rain') => [
            'icon' => 'cloud-rain',
            'emoji' => '🌧️',
            'color' => 'text-blue-500',
        ],
        str_contains($condLower, 'snow') => [
            'icon' => 'snowflake',
            'emoji' => '❄️',
            'color' => 'text-cyan-300',
        ],
        str_contains($condLower, 'thunder') || str_contains($condLower, 'storm') => [
            'icon' => 'cloud-lightning',
            'emoji' => '🌩️',
            'color' => 'text-purple-500',
        ],
        default => [
            'icon' => 'thermometer',
            'emoji' => '🌡️',
            'color' => 'text-slate-500',
        ],
    };

    $trendBadge = match ($trend) {
        'falling' => [
            'icon' => 'trending-down',
            'label' => 'Falling Barometer (' . ($delta ? $delta . ' hPa' : '4-9 PM') . ')',
            'color' => 'text-emerald-600',
        ],
        'rising' => [
            'icon' => 'trending-up',
            'label' => 'Rising Barometer (' . ($delta ? '+' . $delta . ' hPa' : '4-9 PM') . ')',
            'color' => 'text-slate-500',
        ],
        default => [
            'icon' => 'minus',
            'label' => 'Stable Barometer (4-9 PM)',
            'color' => 'text-sky-600',
        ],
    };

    // Temperature-based 2px thick vivid border and background styling (10-degree intervals)
    $tempBorderClass = match (true) {
        is_null($airTemp) => 'border-2 border-slate-300 bg-slate-50 text-slate-700',
        $airTemp < 40 => 'border-2 border-blue-400 bg-blue-50/90 text-blue-950',
        $airTemp < 50 => 'border-2 border-cyan-400 bg-cyan-50/90 text-cyan-950',
        $airTemp < 60 => 'border-2 border-teal-400 bg-teal-50/90 text-teal-950',
        $airTemp < 70 => 'border-2 border-emerald-400 bg-emerald-50/90 text-emerald-950',
        $airTemp < 80 => 'border-2 border-amber-400 bg-amber-50/90 text-amber-950',
        $airTemp < 90 => 'border-2 border-orange-400 bg-orange-50/90 text-orange-950',
        default => 'border-2 border-rose-400 bg-rose-50/90 text-rose-950',
    };

    $tooltipText = $cleanCondition . ' • Air Temp: ' . round($airTemp, 1) . '°F' . ($pressure ? ' (' . round($pressure, 1) . ' hPa)' : '') . ' • 4-9 PM Barometer: ' . $trendBadge['label'];
@endphp

@if($airTemp || $pressure || $condition)
    @if($compact || $size === 'compact')
        <!-- Compact Temperature-Colored Weather + Pressure Badge -->
        <span 
            {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold shadow-2xs transition-all hover:scale-102 cursor-help select-none ' . $tempBorderClass]) }} 
            title="{{ $tooltipText }}"
        >
            <x-dynamic-component :component="'lucide-' . $config['icon']" class="w-3.5 h-3.5 {{ $config['color'] }} shrink-0" />
            <span class="font-mono font-extrabold text-slate-900">{{ round($airTemp, 0) }}°F</span>
            <x-dynamic-component :component="'lucide-' . $trendBadge['icon']" class="w-3 h-3 {{ $trendBadge['color'] }} shrink-0" />
        </span>
    @else
        <!-- Full Temperature-Colored Weather Badge -->
        <div class="inline-flex items-center gap-1.5 flex-wrap">
            <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium shadow-2xs ' . $tempBorderClass]) }}>
                <x-dynamic-component :component="'lucide-' . $config['icon']" class="w-3.5 h-3.5 {{ $config['color'] }} shrink-0" />
                @if($showEmoji)
                    <span class="text-xs">{{ $config['emoji'] }}</span>
                @endif
                @if($airTemp)
                    <span class="font-mono font-bold text-slate-900">{{ round($airTemp, 1) }}°F</span>
                @endif
                @if($pressure)
                    <span class="text-[10px] text-slate-500 font-mono">({{ round($pressure, 1) }} inHg)</span>
                @endif
                @if($condition)
                    <span class="text-[10px] uppercase font-bold text-slate-600 tracking-wider hidden sm:inline">{{ $cleanCondition }}</span>
                @endif
            </div>

            @if($showTrend)
                <x-barometerTrend :weather="$weather" />
            @endif
        </div>
    @endif
@endif
