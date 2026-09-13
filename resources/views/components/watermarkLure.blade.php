@props([
    'category' => 'all',
    'class' => 'absolute inset-0 w-full h-full pointer-events-none select-none overflow-hidden',
])

@php
    $normalized = strtolower(trim((string) $category));
@endphp

<div {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
    <svg viewBox="0 0 400 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full" preserveAspectRatio="xMidYMid slice">
        <defs>
            <linearGradient id="lureMetalGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#ffffff" stop-opacity="0.5" />
                <stop offset="100%" stop-color="#64748b" stop-opacity="0.25" />
            </linearGradient>
        </defs>

        <g transform="translate(270, 90) scale(3.2)" stroke="#64748b" stroke-opacity="0.35" fill="none">
            @if ($normalized === 'crankbait')
                <!-- Diving Bill & Arched Crankbait Body -->
                <path d="M-14 4 L-6 -2" stroke-width="2.2" stroke-linecap="round" />
                <path d="M-9 -2 C-9 -8, -2 -11, 6 -9 C12 -8, 16 -3, 17 0 C16 3, 11 6, 4 5.5 C-3 5, -8 2, -9 -2 Z" stroke-width="2" stroke-linejoin="round" fill="#ffffff" fill-opacity="0.3" />
                <!-- Scales / Ribs -->
                <path d="M0 -7 C2 -5, 2 -1, 0 2" stroke-width="1.4" stroke-linecap="round" stroke-opacity="0.25" />
                <path d="M5 -6 C7 -4, 7 0, 5 3" stroke-width="1.4" stroke-linecap="round" stroke-opacity="0.25" />
                <!-- 3D Eye -->
                <circle cx="-4" cy="-4" r="2" fill="#64748b" fill-opacity="0.4" stroke="none" />
                <!-- Belly Hook -->
                <path d="M1 5.5 V11 C1 14, -2 15, -3 14 M1 11 C1 14, 4 15, 5 14" stroke-width="1.8" stroke-linecap="round" />
                <!-- Tail Hook -->
                <path d="M17 0 L20 4 C21 6, 19 8, 18 7" stroke-width="1.8" stroke-linecap="round" />

            @elseif ($normalized === 'spinnerbait')
                <!-- Overhead Tandem Spinnerbait -->
                <path d="M-12 2 L-4 -8 L8 -8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <line x1="-12" y1="2" x2="0" y2="10" stroke-width="2" stroke-linecap="round" />
                <!-- Willow Blades -->
                <path d="M-1 -8 C2 -11, 6 -11, 8 -8 C6 -5, 2 -5, -1 -8 Z" stroke-width="1.6" fill="#ffffff" fill-opacity="0.35" />
                <path d="M8 -8 C11 -12, 16 -12, 18 -8 C16 -4, 11 -4, 8 -8 Z" stroke-width="1.6" fill="#ffffff" fill-opacity="0.35" />
                <!-- Lead Head -->
                <circle cx="0" cy="10" r="4" fill="#64748b" fill-opacity="0.4" stroke-width="1.5" />
                <!-- Skirt -->
                <path d="M2 10 C7 8, 12 6, 17 8" stroke-width="1.6" stroke-linecap="round" stroke-opacity="0.28" />
                <path d="M2 11 C7 13, 13 15, 18 13" stroke-width="1.6" stroke-linecap="round" stroke-opacity="0.28" />
                <path d="M2 10 C8 11, 13 10, 19 11" stroke-width="1.6" stroke-linecap="round" stroke-opacity="0.28" />

            @elseif ($normalized === 'spoon')
                <!-- Dimpled Metallic Spoon -->
                <circle cx="-12" cy="0" r="2" stroke-width="1.5" />
                <path d="M-9 0 C-7 -6, 1 -8, 9 -7 C13 -6, 15 -3, 14 0 C13 3, 9 7, 0 6 C-7 5, -8 2, -9 0 Z" stroke-width="2" stroke-linejoin="round" fill="#ffffff" fill-opacity="0.3" />
                <circle cx="-3" cy="-2" r="1.2" fill="#64748b" fill-opacity="0.3" stroke="none" />
                <circle cx="3" cy="-3" r="1.2" fill="#64748b" fill-opacity="0.3" stroke="none" />
                <circle cx="2" cy="2" r="1.2" fill="#64748b" fill-opacity="0.3" stroke="none" />
                <circle cx="8" cy="0" r="1.2" fill="#64748b" fill-opacity="0.3" stroke="none" />
                <path d="M14 0 L18 3 C19 5, 18 7, 16 7" stroke-width="1.8" stroke-linecap="round" />

            @elseif ($normalized === 'jig')
                <!-- Leadhead Jig with Weedguard -->
                <circle cx="-7" cy="0" r="5.5" stroke-width="2" fill="#ffffff" fill-opacity="0.3" />
                <circle cx="-8" cy="-1" r="1.8" fill="#64748b" fill-opacity="0.4" stroke="none" />
                <line x1="-5" y1="-5" x2="5" y2="-5" stroke-width="2.2" stroke-linecap="round" />
                <path d="M-1 0 H8 C12 0, 13 -4, 11 -6" stroke-width="2.2" stroke-linecap="round" />
                <path d="M-4 0 C1 3, 6 6, 13 7 M-4 1 C1 4, 5 8, 11 10 M-4 0 C1 2, 7 3, 12 2" stroke-width="1.5" stroke-linecap="round" stroke-opacity="0.28" />

            @elseif ($normalized === 'inline spinner')
                <!-- Inline Spinner -->
                <line x1="-15" y1="0" x2="10" y2="0" stroke-width="2" stroke-linecap="round" />
                <circle cx="-14.5" cy="0" r="1.5" stroke-width="1.6" />
                <path d="M-8 0 C-8 -7, -3 -11, 2 -8 C5 -6, 4 0, -4 0" stroke-width="1.8" stroke-linejoin="round" fill="#ffffff" fill-opacity="0.3" />
                <circle cx="2" cy="0" r="2.2" fill="#64748b" fill-opacity="0.4" stroke="none" />
                <circle cx="7" cy="0" r="2.5" fill="#64748b" fill-opacity="0.4" stroke="none" />
                <path d="M10 0 L15 -4 M10 0 L18 0 M10 0 L15 4" stroke-width="2" stroke-linecap="round" />

            @else
                <!-- Default All / Tackle Lure Icon -->
                <path d="M-12 0 C-10 -7, 0 -9, 8 -7 C13 -5, 15 -1, 14 2 C12 5, 6 8, -2 7 C-8 6, -11 3, -12 0 Z" stroke-width="2" stroke-linejoin="round" fill="#ffffff" fill-opacity="0.3" />
                <circle cx="-6" cy="-2" r="1.8" fill="#64748b" fill-opacity="0.4" stroke="none" />
                <path d="M14 2 L17 6 C18 8, 16 9, 15 8" stroke-width="1.8" stroke-linecap="round" />
                <path d="M0 7 V12 C0 15, -3 16, -4 15" stroke-width="1.8" stroke-linecap="round" />
            @endif
        </g>
    </svg>
</div>
