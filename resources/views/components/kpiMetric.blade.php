@props([
    'label',
    'value',
    'icon' => null,
    'color' => 'teal',
    'subtext' => null,
    'subtextIcon' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

@php
    $colorClasses = match ($color) {
        'emerald' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800/60',
        'sky' => 'bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border-sky-100 dark:border-sky-800/60',
        'amber' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-800/60',
        'purple' => 'bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border-purple-100 dark:border-purple-800/60',
        'rose' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-800/60',
        'indigo' => 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border-indigo-100 dark:border-indigo-800/60',
        default => 'bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border-teal-100 dark:border-teal-800/60',
    };

    $subtextColor = match ($color) {
        'emerald' => 'text-emerald-600 dark:text-emerald-400',
        'sky' => 'text-sky-600 dark:text-sky-400',
        'amber' => 'text-amber-600 dark:text-amber-400',
        'purple' => 'text-purple-600 dark:text-purple-400',
        'rose' => 'text-rose-600 dark:text-rose-400',
        'indigo' => 'text-indigo-600 dark:text-indigo-400',
        default => 'text-teal-600 dark:text-teal-400',
    };

    $safeIcon = match ($icon) {
        'hook' => 'fishing-hook',
        default => $icon,
    };

    $safeSubtextIcon = match ($subtextIcon) {
        'hook' => 'fishing-hook',
        default => $subtextIcon,
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center justify-between space-y-0 transition-colors']) }}>
    <div class="space-y-1">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 block">{{ $label }}</span>
        <span class="text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tight block">{{ is_numeric($value) ? number_format($value) : $value }}</span>
        @if($subtext)
            <span class="text-[11px] font-semibold mt-1 inline-flex items-center gap-1 {{ $subtextColor }}">
                @if($safeSubtextIcon)
                    <x-dynamic-component :component="'lucide-' . $safeSubtextIcon" class="w-3 h-3" />
                @endif
                <span>{{ $subtext }}</span>
            </span>
        @endif
        @if($actionUrl && $actionLabel)
            <a href="{{ $actionUrl }}" class="text-[11px] font-bold text-teal-600 dark:text-teal-400 hover:underline block pt-0.5">
                {{ $actionLabel }}
            </a>
        @endif
    </div>

    @if($safeIcon)
        <div class="w-12 h-12 rounded-2xl border flex items-center justify-center shrink-0 shadow-2xs {{ $colorClasses }}">
            <x-dynamic-component :component="'lucide-' . $safeIcon" class="w-6 h-6" />
        </div>
    @endif
</div>
