@props([
    'weather' => null,
    'showAdvice' => false,
    'compact' => false,
    'size' => 'md',
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

<div class="space-y-2">
    <!-- Tactical Pressure Velocity Pill Badge -->
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border ' . $intel['badge_class']]) }} title="{{ $intel['tactical_advice'] }}">
        <x-dynamic-component :component="'lucide-' . $intel['icon']" class="w-3.5 h-3.5 {{ $intel['icon_color'] }} shrink-0" />
        <span class="font-sans tracking-tight">{{ $intel['badge_label'] }}</span>
    </div>

    <!-- Optional Expanded Tactical Angler Advice Box -->
    @if($showAdvice)
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                    <x-lucide-activity class="w-3 h-3 text-teal-600" />
                    Tactical Feeding Trigger
                </span>
                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-lg {{ $intel['badge_class'] }}">
                    {{ $intel['velocity_3h'] !== null ? ($intel['velocity_3h'] >= 0 ? '+' : '') . $intel['velocity_3h'] . ' hPa/3h' : 'Velocity' }}
                </span>
            </div>
            
            <p class="text-slate-700 font-medium leading-relaxed">
                {{ $intel['tactical_advice'] }}
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 border-t border-slate-200/60 text-[11px]">
                <div>
                    <span class="text-slate-400 font-semibold block">Target Depth:</span>
                    <strong class="text-slate-800">{{ $intel['target_species_depth'] }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Recommended Tackle:</span>
                    <strong class="text-teal-700">{{ $intel['lure_recommendations'] }}</strong>
                </div>
            </div>
        </div>
    @endif
</div>
