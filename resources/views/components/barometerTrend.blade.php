@props([
    'weather' => null,
])

@php
    if (!$weather) {
        return;
    }

    $service = app(\Fishinglog\Services\WeatherTelemetryService::class);
    $hourly = is_object($weather) ? ($weather->hourly_telemetry ?? null) : ($weather['hourly_telemetry'] ?? null);
    $delta = is_object($weather) ? ($weather->window_pressure_delta ?? null) : ($weather['window_pressure_delta'] ?? null);
    $trend = is_object($weather) ? ($weather->pressure_trend ?? null) : ($weather['pressure_trend'] ?? null);

    $intel = $service->calculatePressureVelocity(
        is_array($hourly) ? $hourly : null,
        $delta,
        $trend
    );
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border shadow-xs ' . $intel['badge_class']]) }} title="{{ $intel['tactical_advice'] }}">
    <x-dynamic-component :component="'lucide-' . $intel['icon']" class="w-3.5 h-3.5 {{ $intel['icon_color'] }} shrink-0" />
    <span>{{ $intel['badge_label'] }}</span>
</span>
