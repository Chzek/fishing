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

        <!-- Line Running Along the Blank -->
        <path d="M 276 138 L 288 78 L 294 44 L 282 16 L 254 18 L 234 38 L 220 56" stroke="#64748b" stroke-opacity="0.2" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />

        <!-- Minimalist Rod Blank (Deep Hooked Parabolic Bend) -->
        <path d="M 292 176 L 296 112 C 298 62, 316 20, 286 12 C 260 6, 235 28, 220 56" stroke="#64748b" stroke-opacity="0.32" stroke-width="3.5" stroke-linecap="round" />

        <!-- Minimalist Guide Rings (Clean, bold, un-cluttered circles) -->
        <circle cx="288" cy="78" r="5" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.6" />
        <circle cx="294" cy="44" r="4.2" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.5" />
        <circle cx="282" cy="16" r="3.6" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.4" />
        <circle cx="254" cy="18" r="3.2" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.3" />
        <circle cx="234" cy="38" r="2.8" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.2" />
        <circle cx="220" cy="56" r="2.4" fill="#64748b" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.2" />

        <!-- Minimalist Spinning Reel Silhouette -->
        <!-- Reel Stem -->
        <line x1="294" y1="138" x2="280" y2="138" stroke="#64748b" stroke-opacity="0.32" stroke-width="2" stroke-linecap="round" />
        <!-- Reel Spool -->
        <rect x="264" y="128" width="14" height="18" rx="3" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.6" />
        <!-- Reel Body & Handle -->
        <circle cx="282" cy="144" r="5" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.4" />
        <path d="M 284 148 L 290 155" stroke="#64748b" stroke-opacity="0.32" stroke-width="2" stroke-linecap="round" />
        <circle cx="291" cy="157" r="2" fill="#64748b" fill-opacity="0.35" />

        <!-- Minimalist Rod Handle Grip & Butt Cap -->
        <rect x="289" y="108" width="7" height="60" rx="3.5" fill="#ffffff" fill-opacity="0.35" stroke="#64748b" stroke-opacity="0.3" stroke-width="1.5" />
        <rect x="288" y="168" width="9" height="5" rx="2" fill="#64748b" fill-opacity="0.35" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.2" />
    </svg>
</div>
