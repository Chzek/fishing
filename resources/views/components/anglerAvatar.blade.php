@props([
    'angler' => null,
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

    $user = $angler->user ?? null;
    
    $avatarFile = null;
    if ($angler && $angler->avatar && (file_exists(public_path('storage/avatars/' . $angler->avatar)) || \Illuminate\Support\Facades\Storage::disk('public')->exists('avatars/' . $angler->avatar))) {
        $avatarFile = $angler->avatar;
    } elseif ($user && $user->avatar && (file_exists(public_path('storage/avatars/' . $user->avatar)) || \Illuminate\Support\Facades\Storage::disk('public')->exists('avatars/' . $user->avatar))) {
        $avatarFile = $user->avatar;
    }

    $initials = '';
    if ($angler) {
        $first = substr($angler->firstName ?? '', 0, 1);
        $last = substr($angler->lastName ?? '', 0, 1);
        $initials = strtoupper($first . $last);
    }
    if (empty($initials)) {
        $initials = '?';
    }

    // Deterministic palette selection based on angler ID and name hash
    $palettes = [
        ['bg' => 'bg-teal-500/15', 'border' => 'border-teal-500/30', 'text' => 'text-teal-700'],
        ['bg' => 'bg-sky-500/15', 'border' => 'border-sky-500/30', 'text' => 'text-sky-700'],
        ['bg' => 'bg-emerald-500/15', 'border' => 'border-emerald-500/30', 'text' => 'text-emerald-700'],
        ['bg' => 'bg-amber-500/15', 'border' => 'border-amber-500/30', 'text' => 'text-amber-800'],
        ['bg' => 'bg-indigo-500/15', 'border' => 'border-indigo-500/30', 'text' => 'text-indigo-700'],
        ['bg' => 'bg-purple-500/15', 'border' => 'border-purple-500/30', 'text' => 'text-purple-700'],
        ['bg' => 'bg-rose-500/15', 'border' => 'border-rose-500/30', 'text' => 'text-rose-700'],
        ['bg' => 'bg-cyan-500/15', 'border' => 'border-cyan-500/30', 'text' => 'text-cyan-700'],
    ];

    $anglerId = $angler->id ?? 0;
    $fullName = ($angler->firstName ?? '') . ($angler->lastName ?? '');
    $hash = abs(crc32($fullName . '_' . $anglerId));
    $color = $palettes[$hash % count($palettes)];
@endphp

@if($avatarFile)
    <img src="/storage/avatars/{{ $avatarFile }}" 
         alt="{{ $angler->firstName ?? '' }} {{ $angler->lastName ?? '' }}" 
         class="{{ $sizeClasses }} rounded-full object-cover border border-slate-200/80 shrink-0 shadow-sm {{ $class }}">

@else
    <div class="{{ $sizeClasses }} rounded-full {{ $color['bg'] }} border {{ $color['border'] }} {{ $color['text'] }} font-bold flex items-center justify-center tracking-wider uppercase shrink-0 shadow-sm {{ $class }}"
         title="{{ $angler->firstName ?? '' }} {{ $angler->lastName ?? '' }}">
        {{ $initials }}
    </div>
@endif
