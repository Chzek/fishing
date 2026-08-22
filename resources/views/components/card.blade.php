@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'iconColor' => 'teal',
    'badge' => null,
    'padding' => 'p-5',
])

@php
    $iconColorClasses = match ($iconColor) {
        'sky' => 'bg-sky-50 text-sky-600 border-sky-100',
        'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
        'rose' => 'bg-rose-50 text-rose-600 border-rose-100',
        'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'purple' => 'bg-purple-50 text-purple-600 border-purple-100',
        default => 'bg-teal-50 text-teal-600 border-teal-100',
    };
@endphp

<div {{ $attributes->merge(['class' => "bg-white rounded-2xl border border-slate-200/80 shadow-sm {$padding} space-y-4"]) }}>
    @if($title || $icon || isset($actions))
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 gap-3">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="w-9 h-9 rounded-xl border flex items-center justify-center shrink-0 {{ $iconColorClasses }}">
                        <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                    </div>
                @endif
                <div>
                    @if($title)
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight flex items-center gap-2">
                            <span>{{ $title }}</span>
                            @if($badge)
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700 font-mono">{{ $badge }}</span>
                            @endif
                        </h3>
                    @endif
                    @if($subtitle)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            @if(isset($actions))
                <div class="shrink-0 flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="pt-3 border-t border-slate-100 text-xs text-slate-500">
            {{ $footer }}
        </div>
    @endif
</div>
