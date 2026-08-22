@props([
    'weather' => null,
    'size' => 'md',
])

@php
    if (!$weather) {
        return;
    }

    $airTemp = is_object($weather) ? ($weather->air_temp_mean ?? $weather->air_temp ?? null) : ($weather['air_temp_mean'] ?? $weather['air_temp'] ?? null);
    $pressure = is_object($weather) ? ($weather->barometric_pressure ?? null) : ($weather['barometric_pressure'] ?? null);
    $condition = is_object($weather) ? ($weather->weather_condition ?? null) : ($weather['weather_condition'] ?? null);

    $iconName = match (strtolower((string) $condition)) {
        'clear', 'sunny' => 'sun',
        'partly_cloudy', 'cloudy' => 'cloud-sun',
        'overcast' => 'cloud',
        'rain', 'rainy', 'drizzle' => 'cloud-rain',
        'thunderstorm' => 'cloud-lightning',
        'snow', 'snowy' => 'snowflake',
        default => 'thermometer',
    };
@endphp

@if($airTemp || $pressure || $condition)
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-2.5 py-1 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-700 font-medium shadow-2xs']) }}>
        <i data-lucide="{{ $iconName }}" class="w-3.5 h-3.5 text-sky-600 shrink-0"></i>
        @if($airTemp)
            <span class="font-mono font-bold text-slate-900">{{ round($airTemp, 1) }}°F</span>
        @endif
        @if($pressure)
            <span class="text-[10px] text-slate-500 font-mono">({{ round($pressure, 1) }} inHg)</span>
        @endif
        @if($condition)
            <span class="text-[10px] uppercase font-bold text-sky-700 tracking-wider hidden sm:inline">{{ str_replace('_', ' ', $condition) }}</span>
        @endif
    </div>
@endif
