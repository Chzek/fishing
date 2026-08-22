@props([
    'fish' => null,
    'size' => 'md',
    'class' => '',
])

@php
    $sizeClasses = [
        'xs' => 'w-7 h-7 text-[10px]',
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-xs',
        'lg' => 'w-14 h-14 text-sm',
        'xl' => 'w-20 h-20 text-base',
    ][$size] ?? 'w-10 h-10 text-xs';

    $iconSizes = [
        'xs' => 'w-3.5 h-3.5',
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-7 h-7',
        'xl' => 'w-10 h-10',
    ][$size] ?? 'w-5 h-5';

    $avatarUrl = $fish->avatarUrl ?? null;
    $imageUrl = $fish->imageUrl ?? null;
    $displayImage = $avatarUrl ?: $imageUrl;

    // Palette selection based on fish family or species name hash
    $palettes = [
        ['bg' => 'bg-teal-500/15', 'border' => 'border-teal-500/30', 'text' => 'text-teal-600'],
        ['bg' => 'bg-emerald-500/15', 'border' => 'border-emerald-500/30', 'text' => 'text-emerald-600'],
        ['bg' => 'bg-sky-500/15', 'border' => 'border-sky-500/30', 'text' => 'text-sky-600'],
        ['bg' => 'bg-amber-500/15', 'border' => 'border-amber-500/30', 'text' => 'text-amber-600'],
        ['bg' => 'bg-indigo-500/15', 'border' => 'border-indigo-500/30', 'text' => 'text-indigo-600'],
        ['bg' => 'bg-purple-500/15', 'border' => 'border-purple-500/30', 'text' => 'text-purple-600'],
    ];

    $hash = abs(crc32(($fish->name ?? 'fish') . '_' . ($fish->id ?? 0)));
    $color = $palettes[$hash % count($palettes)];
@endphp

@if($displayImage)
    <div class="{{ $sizeClasses }} rounded-xl bg-slate-50 border border-slate-200/80 p-0.5 flex items-center justify-center shrink-0 shadow-sm overflow-hidden {{ $class }}">
        <img src="{{ $displayImage }}" 
             alt="{{ $fish->name ?? 'Fish' }}" 
             class="w-full h-full object-contain filter drop-shadow-sm">
    </div>
@else
    <div class="{{ $sizeClasses }} rounded-xl {{ $color['bg'] }} border {{ $color['border'] }} {{ $color['text'] }} font-bold flex items-center justify-center shrink-0 shadow-sm {{ $class }}"
         title="{{ $fish->name ?? 'Fish' }}">
        <i data-lucide="fish" class="{{ $iconSizes }}"></i>
    </div>
@endif
