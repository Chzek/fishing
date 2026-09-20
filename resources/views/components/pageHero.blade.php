@props([
    'title',
    'subtitle' => null,
    'icon' => 'anchor',
    'iconColor' => 'teal',
    'badge' => null,
    'badgeVariant' => 'teal',
])

@php
    $iconColorClasses = match ($iconColor) {
        'emerald' => 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
        'sky' => 'bg-sky-500/10 border-sky-500/30 text-sky-400',
        'amber' => 'bg-amber-500/10 border-amber-500/30 text-amber-400',
        'purple' => 'bg-purple-500/10 border-purple-500/30 text-purple-400',
        'rose' => 'bg-rose-500/10 border-rose-500/30 text-rose-400',
        'indigo' => 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400',
        default => 'bg-teal-500/10 border-teal-500/30 text-teal-400',
    };

    $normalizedIcon = $icon ? (str_starts_with($icon, 'lucide-') ? substr($icon, 7) : $icon) : null;
    $safeIcon = match ($normalizedIcon) {
        'hook' => 'fishing-hook',
        default => $normalizedIcon,
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 space-y-5']) }}>
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5 min-w-0">
            @if($safeIcon)
                <div class="w-12 h-12 rounded-2xl border flex items-center justify-center shrink-0 shadow-inner {{ $iconColorClasses }}">
                    <x-dynamic-component :component="'lucide-' . $safeIcon" class="w-6 h-6" />
                </div>
            @endif
            <div class="min-w-0">
                <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2.5 flex-wrap">
                    <span>{{ $title }}</span>
                    @if($badge)
                        <x-badge :variant="$badgeVariant" size="sm" fontMono>{{ $badge }}</x-badge>
                    @endif
                </h1>
                @if($subtitle)
                    <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if(isset($actions))
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                {{ $actions }}
            </div>
        @endif
    </div>

    @if(isset($metrics))
        <div class="pt-3.5 border-t border-slate-800">
            {{ $metrics }}
        </div>
    @endif

    {{ $slot }}
</div>
