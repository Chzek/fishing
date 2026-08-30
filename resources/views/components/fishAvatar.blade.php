@props([
    'breed' => null,
    'fish' => null,
    'size' => 'md',
])

@php
    $breed = $breed ?? $fish;
    $dimensions = match($size) {
        'xs' => 'w-5 h-5 text-[10px]',
        'sm' => 'w-7 h-7 text-xs',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-14 h-14 text-lg',
        '2xl' => 'w-16 h-16 text-xl',
        default => 'w-9 h-9 text-sm',
    };

    $name = $breed?->name ?? 'Fish';
    $family = strtolower($breed?->family?->name ?? '');

    $ringAccent = match(true) {
        str_contains($family, 'pike') => 'ring-1 ring-emerald-400/60 border-emerald-500/80',
        str_contains($family, 'perch') => 'ring-1 ring-amber-400/60 border-amber-500/80',
        str_contains($family, 'salmon') || str_contains($family, 'trout') => 'ring-1 ring-sky-400/60 border-sky-500/80',
        str_contains($family, 'sunfish') || str_contains($family, 'bass') => 'ring-1 ring-indigo-400/60 border-indigo-500/80',
        default => 'ring-1 ring-slate-400/60 border-slate-500/80',
    };

    $avatarUrl = $breed?->avatar_url;

    if (!$avatarUrl && !empty($name)) {
        $slug = \Illuminate\Support\Str::slug(strtolower($name), '_');
        foreach (['svg', 'png', 'jpg', 'jpeg'] as $ext) {
            if (file_exists(public_path('images/fish/avatars/' . $slug . '.' . $ext))) {
                $avatarUrl = asset('images/fish/avatars/' . $slug . '.' . $ext);
                break;
            } elseif (file_exists(public_path('images/fish/' . $slug . '.' . $ext))) {
                $avatarUrl = asset('images/fish/' . $slug . '.' . $ext);
                break;
            }
        }
    }
@endphp

<div {{ $attributes->merge(['class' => "relative inline-flex items-center justify-center shrink-0 rounded-full bg-slate-900 border shadow-xs overflow-hidden {$dimensions} {$ringAccent}"]) }} title="{{ $name }}">
    @if($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="w-full h-full object-cover rounded-full transition-transform duration-200 hover:scale-110" />
    @else
        <i data-lucide="fish" class="w-3/5 h-3/5 shrink-0 text-slate-300"></i>
    @endif
</div>
