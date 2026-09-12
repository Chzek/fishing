@props([
    'class' => 'absolute right-0 bottom-0 w-44 h-44 sm:w-48 sm:h-48 pointer-events-none select-none overflow-hidden',
])

<div {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
    <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
        <!-- Top Weighing Pan / Tray -->
        <rect x="18" y="16" width="104" height="12" rx="6" fill="#ffffff" fill-opacity="0.35" stroke="#64748b" stroke-opacity="0.32" stroke-width="3.5" stroke-linejoin="round" />

        <!-- Stand Neck Support Column -->
        <path d="M 61 28 L 61 40 L 79 40 L 79 28" fill="#ffffff" fill-opacity="0.3" stroke="#64748b" stroke-opacity="0.32" stroke-width="3" stroke-linejoin="round" />

        <!-- Scale Housing (Vintage Rounded Trapezoid Body) -->
        <path d="M 48 40 L 22 124 C 20.5 128, 24 132, 28 132 L 112 132 C 116 132, 119.5 128, 118 124 L 92 40 C 91 37.5, 88.5 36, 85 36 L 55 36 C 51.5 36, 49 37.5, 48 40 Z" fill="#ffffff" fill-opacity="0.25" stroke="#64748b" stroke-opacity="0.32" stroke-width="3.5" stroke-linejoin="round" />

        <!-- Circular Dial Bezel & Face -->
        <circle cx="70" cy="86" r="30" fill="#ffffff" fill-opacity="0.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="3" />

        <!-- Dial Radial Ticks (12 points around clock face) -->
        <!-- 12 o'clock -->
        <line x1="70" y1="60" x2="70" y2="66" stroke="#64748b" stroke-opacity="0.4" stroke-width="3" stroke-linecap="round" />
        <!-- 1 o'clock (30°) -->
        <line x1="82.5" y1="63.3" x2="79.5" y2="68.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="2.25" stroke-linecap="round" />
        <!-- 2 o'clock (60°) -->
        <line x1="91.7" y1="72.5" x2="86.5" y2="75.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="2.25" stroke-linecap="round" />
        <!-- 3 o'clock (90°) -->
        <line x1="95" y1="86" x2="89" y2="86" stroke="#64748b" stroke-opacity="0.4" stroke-width="3" stroke-linecap="round" />
        <!-- 4 o'clock (120°) -->
        <line x1="91.7" y1="99.5" x2="86.5" y2="96.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="2.25" stroke-linecap="round" />
        <!-- 5 o'clock (150°) -->
        <line x1="82.5" y1="108.7" x2="79.5" y2="103.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="2.25" stroke-linecap="round" />
        <!-- 6 o'clock (180°) -->
        <line x1="70" y1="112" x2="70" y2="106" stroke="#64748b" stroke-opacity="0.4" stroke-width="3" stroke-linecap="round" />
        <!-- 7 o'clock (210°) -->
        <line x1="57.5" y1="108.7" x2="60.5" y2="103.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="2.25" stroke-linecap="round" />
        <!-- 8 o'clock (240°) -->
        <line x1="48.3" y1="99.5" x2="53.5" y2="96.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="2.25" stroke-linecap="round" />
        <!-- 9 o'clock (270°) -->
        <line x1="45" y1="86" x2="51" y2="86" stroke="#64748b" stroke-opacity="0.4" stroke-width="3" stroke-linecap="round" />
        <!-- 10 o'clock (300°) -->
        <line x1="48.3" y1="72.5" x2="53.5" y2="75.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="2.25" stroke-linecap="round" />
        <!-- 11 o'clock (330°) -->
        <line x1="57.5" y1="63.3" x2="60.5" y2="68.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="2.25" stroke-linecap="round" />

        <!-- Pointer Needle pointing to ~2:15 position (angled up-right) -->
        <line x1="70" y1="86" x2="88" y2="69" stroke="#64748b" stroke-opacity="0.45" stroke-width="3.5" stroke-linecap="round" />

        <!-- Center Hub Pin -->
        <circle cx="70" cy="86" r="4" fill="#64748b" fill-opacity="0.45" stroke="none" />
    </svg>
</div>
