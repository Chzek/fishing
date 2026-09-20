@props([
    'variant' => 'teal',
    'size' => 'md',
    'icon' => null,
    'label' => null,
    'fontMono' => false,
])

@php
    $variantClasses = match ($variant) {
        'emerald' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
        'sky' => 'bg-sky-50 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 border-sky-200 dark:border-sky-800',
        'amber' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 border-amber-200 dark:border-amber-800',
        'purple' => 'bg-purple-50 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border-purple-200 dark:border-purple-800',
        'rose' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800',
        'indigo' => 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
        'slate' => 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        default => 'bg-teal-50 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300 border-teal-200 dark:border-teal-800',
    };

    $sizeClasses = match ($size) {
        'sm' => 'text-[10px] px-2 py-0.5 gap-1',
        'lg' => 'text-xs px-3 py-1.5 gap-1.5',
        default => 'text-xs px-2.5 py-1 gap-1.5',
    };

    $iconSizes = match ($size) {
        'sm' => 'w-3 h-3',
        'lg' => 'w-4 h-4',
        default => 'w-3.5 h-3.5',
    };

    $fontFamily = $fontMono ? 'font-mono font-bold' : 'font-semibold';
    $normalizedIcon = $icon ? (str_starts_with($icon, 'lucide-') ? substr($icon, 7) : $icon) : null;
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border shadow-2xs {$fontFamily} {$variantClasses} {$sizeClasses}"]) }}>
    @if($normalizedIcon)
        <x-dynamic-component :component="'lucide-' . $normalizedIcon" class="{{ $iconSizes }} shrink-0" />
    @endif
    <span>{{ $label ?? $slot }}</span>
</span>
