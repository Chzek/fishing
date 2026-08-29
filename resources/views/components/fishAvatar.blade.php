@props([
    'breed' => null,
    'size' => 'md',
])

@php
    $dimensions = match($size) {
        'xs' => 'w-4 h-4 text-[10px]',
        'sm' => 'w-6 h-6 text-xs',
        'lg' => 'w-10 h-10 text-base',
        default => 'w-8 h-8 text-sm',
    };

    $name = $breed?->name ?? 'Fish';
    $family = strtolower($breed?->family?->name ?? '');

    $badgeStyle = match(true) {
        str_contains($family, 'pike') => 'bg-emerald-50 border-emerald-300/80 text-emerald-800',
        str_contains($family, 'perch') => 'bg-amber-50 border-amber-300/80 text-amber-800',
        str_contains($family, 'salmon') || str_contains($family, 'trout') => 'bg-sky-50 border-sky-300/80 text-sky-800',
        str_contains($family, 'sunfish') || str_contains($family, 'bass') => 'bg-indigo-50 border-indigo-300/80 text-indigo-800',
        default => 'bg-slate-100 border-slate-300/80 text-slate-800',
    };

    $imageFile = match(true) {
        str_contains(strtolower($name), 'pike') => 'northern_pike.jpg',
        str_contains(strtolower($name), 'muskellunge') => 'northern_pike.jpg',
        str_contains(strtolower($name), 'walleye') => 'walleye.jpg',
        str_contains(strtolower($name), 'bass') => 'largemouth_bass.jpg',
        str_contains(strtolower($name), 'trout') || str_contains(strtolower($name), 'salmon') => 'rainbow_trout.jpg',
        default => null,
    };
@endphp

<div {{ $attributes->merge(['class' => "relative inline-flex items-center justify-center shrink-0 rounded-full border shadow-2xs overflow-hidden {$dimensions} {$badgeStyle}"]) }} title="{{ $name }}">
    @if($imageFile && file_exists(public_path('images/fish/' . $imageFile)))
        <img src="{{ asset('images/fish/' . $imageFile) }}" alt="{{ $name }}" class="w-full h-full object-cover rounded-full" />
    @else
        <i data-lucide="fish" class="w-3/5 h-3/5 shrink-0 opacity-80"></i>
    @endif
</div>
