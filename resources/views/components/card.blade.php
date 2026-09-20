@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'iconColor' => 'teal',
    'badge' => null,
    'badgeVariant' => 'teal',
    'actionUrl' => null,
    'actionLabel' => null,
])

@php
    $iconColorClasses = match ($iconColor) {
        'emerald' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        'sky' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
        'amber' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        'purple' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
        'rose' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        'indigo' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
        'slate' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
        default => 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
    };

    $normalizedIcon = $icon ? (str_starts_with($icon, 'lucide-') ? substr($icon, 7) : $icon) : null;
    $safeIcon = match ($normalizedIcon) {
        'hook' => 'fishing-hook',
        default => $normalizedIcon,
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 rounded-2xl p-5 shadow-sm border border-slate-200/80 dark:border-slate-800 space-y-4']) }}>
    @if($title || $icon || isset($actions) || $actionUrl || $badge)
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3 gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                @if($safeIcon)
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 border {{ $iconColorClasses }}">
                        <x-dynamic-component :component="'lucide-' . $safeIcon" class="w-4 h-4" />
                    </div>
                @endif
                <div class="min-w-0">
                    @if($title)
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm tracking-tight truncate">{{ $title }}</h2>
                    @endif
                    @if($subtitle)
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($badge)
                    <x-badge :variant="$badgeVariant" size="sm" fontMono>{{ $badge }}</x-badge>
                @endif

                @if(isset($actions))
                    {{ $actions }}
                @elseif($actionUrl && $actionLabel)
                    <a href="{{ $actionUrl }}" class="text-xs font-bold text-teal-600 dark:text-teal-400 hover:underline">
                        {{ $actionLabel }}
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{ $slot }}

    @if(isset($footer))
        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
            {{ $footer }}
        </div>
    @endif
</div>
