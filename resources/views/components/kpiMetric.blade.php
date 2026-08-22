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
        'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'sky' => 'bg-sky-50 text-sky-600 border-sky-100',
        'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
        'purple' => 'bg-purple-50 text-purple-600 border-purple-100',
        'rose' => 'bg-rose-50 text-rose-600 border-rose-100',
        'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
        default => 'bg-teal-50 text-teal-600 border-teal-100',
    };

    $subtextColor = match ($color) {
        'emerald' => 'text-emerald-600',
        'sky' => 'text-sky-600',
        'amber' => 'text-amber-600',
        'purple' => 'text-purple-600',
        'rose' => 'text-rose-600',
        'indigo' => 'text-indigo-600',
        default => 'text-teal-600',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between space-y-0']) }}>
    <div class="space-y-1">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">{{ $label }}</span>
        <span class="text-3xl font-black text-slate-900 font-mono tracking-tight block">{{ is_numeric($value) ? number_format($value) : $value }}</span>
        @if($subtext)
            <span class="text-[11px] font-semibold mt-1 inline-flex items-center gap-1 {{ $subtextColor }}">
                @if($subtextIcon)
                    <i data-lucide="{{ $subtextIcon }}" class="w-3 h-3"></i>
                @endif
                <span>{{ $subtext }}</span>
            </span>
        @endif
        @if($actionUrl && $actionLabel)
            <a href="{{ $actionUrl }}" class="text-[11px] font-bold text-teal-600 hover:underline block pt-0.5">
                {{ $actionLabel }}
            </a>
        @endif
    </div>

    @if($icon)
        <div class="w-12 h-12 rounded-2xl border flex items-center justify-center shrink-0 shadow-2xs {{ $colorClasses }}">
            <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
        </div>
    @endif
</div>
