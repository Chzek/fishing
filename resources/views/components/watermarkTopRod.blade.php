@props([
    'class' => 'absolute inset-0 w-full h-full pointer-events-none select-none overflow-hidden',
])

<div {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
    <svg viewBox="0 0 400 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full" preserveAspectRatio="xMidYMid slice">
        <!-- Subtle Water Entry Ripple (Bottom-Left) -->
        <ellipse cx="28" cy="166" rx="22" ry="4" stroke="#64748b" stroke-opacity="0.22" stroke-width="1.5" stroke-dasharray="4 3" />
        <ellipse cx="28" cy="166" rx="10" ry="2" stroke="#64748b" stroke-opacity="0.25" stroke-width="1.2" />

        <!-- Taut Fishing Line (Shooting diagonally from tip to bottom-left) -->
        <line x1="220" y1="56" x2="28" y2="166" stroke="#64748b" stroke-opacity="0.35" stroke-width="2" stroke-linecap="round" />

        <!-- Line Running Along the Blank into Reel Spool -->
        <path d="M 268 106 L 288 78 L 294 44 L 282 16 L 254 18 L 234 38 L 220 56" stroke="#64748b" stroke-opacity="0.22" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />

        <!-- Minimalist Rod Blank (Deep Hooked Parabolic Bend) -->
        <path d="M 295 175 L 295 105 C 298 62, 316 20, 286 12 C 260 6, 235 28, 220 56" stroke="#64748b" stroke-opacity="0.32" stroke-width="3.5" stroke-linecap="round" />

        <!-- Minimalist Guide Rings -->
        <circle cx="288" cy="78" r="5" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.6" />
        <circle cx="294" cy="44" r="4.2" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.5" />
        <circle cx="282" cy="16" r="3.6" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.4" />
        <circle cx="254" cy="18" r="3.2" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.3" />
        <circle cx="234" cy="38" r="2.8" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.2" />
        <circle cx="220" cy="56" r="2.4" fill="#64748b" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.2" />

        <!-- Ported Fly / Centerpin Reel Assembly -->
        <!-- Reel Foot & Stem Mount -->
        <line x1="295" y1="116" x2="278" y2="120" stroke="#64748b" stroke-opacity="0.38" stroke-width="2.5" stroke-linecap="round" />

        <!-- Circular Reel Housing -->
        <circle cx="268" cy="120" r="14" fill="#ffffff" fill-opacity="0.45" stroke="#64748b" stroke-opacity="0.38" stroke-width="2" />
        <circle cx="268" cy="120" r="12" stroke="#64748b" stroke-opacity="0.2" stroke-width="1" fill="none" />

        <!-- Center Hub Pin -->
        <circle cx="268" cy="120" r="2.2" fill="#64748b" fill-opacity="0.45" />

        <!-- 6 Radial Porting Holes -->
        <circle cx="275.5" cy="120" r="2.2" fill="#64748b" fill-opacity="0.35" />
        <circle cx="271.75" cy="126.5" r="2.2" fill="#64748b" fill-opacity="0.35" />
        <circle cx="264.25" cy="126.5" r="2.2" fill="#64748b" fill-opacity="0.35" />
        <circle cx="260.5" cy="120" r="2.2" fill="#64748b" fill-opacity="0.35" />
        <circle cx="264.25" cy="113.5" r="2.2" fill="#64748b" fill-opacity="0.35" />
        <circle cx="271.75" cy="113.5" r="2.2" fill="#64748b" fill-opacity="0.35" />

        <!-- Rod Handle Grip & Reel Seat Collar -->
        <rect x="291.5" y="125" width="7" height="48" rx="3.5" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.6" />
        <!-- Reel Seat Collar / Band -->
        <rect x="289" y="121" width="12" height="6" rx="1.5" fill="#64748b" fill-opacity="0.35" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.2" />
        <!-- Foregrip Collar -->
        <rect x="292" y="105" width="6" height="14" rx="2.5" fill="#ffffff" fill-opacity="0.35" stroke="#64748b" stroke-opacity="0.3" stroke-width="1.2" />
    </svg>
</div>
