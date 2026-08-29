@props([
    'weather' => null,
])

@php
    if (!$weather) {
        return;
    }

    $trend = is_object($weather) ? ($weather->pressure_trend ?? null) : ($weather['pressure_trend'] ?? null);
    $delta = is_object($weather) ? ($weather->window_pressure_delta ?? null) : ($weather['window_pressure_delta'] ?? null);

    if (!$trend && is_null($delta)) {
        return;
    }

    $badge = match ($trend) {
        'falling' => [
            'label' => 'Falling Barometer (' . ($delta ? $delta . ' hPa' : '4-9 PM') . ')',
            'icon' => 'trending-down',
            'class' => 'bg-emerald-50 text-emerald-800 border-emerald-200/90',
            'iconColor' => 'text-emerald-600',
        ],
        'rising' => [
            'label' => 'Rising Barometer (' . ($delta ? '+' . $delta . ' hPa' : '4-9 PM') . ')',
            'icon' => 'trending-up',
            'class' => 'bg-slate-100 text-slate-700 border-slate-200',
            'iconColor' => 'text-slate-500',
        ],
        default => [
            'label' => 'Stable Barometer (4-9 PM)',
            'icon' => 'minus',
            'class' => 'bg-sky-50 text-sky-800 border-sky-200/90',
            'iconColor' => 'text-sky-600',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border shadow-xs ' . $badge['class']]) }} title="Prime Fishing Window (4-9 PM) Barometric Pressure Movement">
    <i data-lucide="{{ $badge['icon'] }}" class="w-3.5 h-3.5 {{ $badge['iconColor'] }} shrink-0"></i>
    <span>{{ $badge['label'] }}</span>
</span>
