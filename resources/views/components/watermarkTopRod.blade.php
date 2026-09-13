@props([
    'class' => 'absolute inset-0 w-full h-full pointer-events-none select-none overflow-hidden',
])

<div {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
    <svg viewBox="0 0 400 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full" preserveAspectRatio="xMidYMid slice">
        <defs>
            <linearGradient id="corkGripGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#ffffff" stop-opacity="0.5" />
                <stop offset="100%" stop-color="#64748b" stop-opacity="0.2" />
            </linearGradient>
            <linearGradient id="reelMetalGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#ffffff" stop-opacity="0.55" />
                <stop offset="100%" stop-color="#64748b" stop-opacity="0.25" />
            </linearGradient>
        </defs>

        <!-- SUBTLE WATER RIPPLES -->
        <g opacity="0.7">
            <ellipse cx="45" cy="166" rx="32" ry="5" stroke="#64748b" stroke-opacity="0.2" stroke-width="1.2" stroke-dasharray="5 4" fill="#ffffff" fill-opacity="0.15" />
            <ellipse cx="45" cy="166" rx="16" ry="2.8" stroke="#64748b" stroke-opacity="0.25" stroke-width="1" fill="none" />
            <path d="M 38 162 Q 42 156 46 162" stroke="#64748b" stroke-opacity="0.22" stroke-width="1" fill="none" />
        </g>

        <!-- TAUT FISHING LINE (Pulling from curved rod tip down into water) -->
        <line x1="68" y1="108" x2="45" y2="166" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.8" stroke-linecap="round" />
        <line x1="69" y1="108" x2="46" y2="166" stroke="#ffffff" stroke-opacity="0.4" stroke-width="1" stroke-linecap="round" />

        <!-- FISHING LINE THROUGH GUIDES (From Reel Spool through Guides to Tip) -->
        <path d="
            M 312 128 
            L 268 64 
            L 220 32 
            L 172 18 
            L 128 20 
            L 94 38 
            L 74 68 
            L 68 108
        " stroke="#64748b" stroke-opacity="0.28" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none" />

        <!-- LOADED PARABOLIC ROD BLANK (Deep loaded fighting bend) -->
        <!-- Spine stroke -->
        <path d="
            M 380 175 
            L 305 98 
            C 255 46, 190 8, 130 18 
            C 92 24, 68 56, 68 108
        " stroke="#64748b" stroke-opacity="0.32" stroke-width="4" stroke-linecap="round" fill="none" />
        
        <!-- Core highlight line -->
        <path d="
            M 380 175 
            L 305 98 
            C 255 46, 190 8, 130 18 
            C 92 24, 68 56, 68 108
        " stroke="#ffffff" stroke-opacity="0.45" stroke-width="1.8" stroke-linecap="round" fill="none" />

        <!-- ROD GUIDES & GUIDE WRAPS -->
        <!-- Stripper Guide at (268, 64) -->
        <g transform="translate(268, 64) rotate(-38)">
            <rect x="-7" y="2" width="14" height="2.5" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="-4" y1="2" x2="-2.5" y2="-9" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.6" />
            <line x1="4" y1="2" x2="2.5" y2="-9" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.6" />
            <circle cx="0" cy="-11" r="6.5" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.8" />
        </g>

        <!-- Guide 2 at (220, 32) -->
        <g transform="translate(220, 32) rotate(-22)">
            <rect x="-5" y="2" width="10" height="2" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="-2.5" y1="2" x2="-1.5" y2="-7.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.4" />
            <line x1="2.5" y1="2" x2="1.5" y2="-7.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.4" />
            <circle cx="0" cy="-9" r="5" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.6" />
        </g>

        <!-- Guide 3 at (172, 18) -->
        <g transform="translate(172, 18) rotate(-4)">
            <rect x="-4" y="2" width="8" height="2" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="-2" y1="2" x2="-1" y2="-6.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.3" />
            <line x1="2" y1="2" x2="1" y2="-6.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.3" />
            <circle cx="0" cy="-7.5" r="4.4" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.5" />
        </g>

        <!-- Guide 4 at (128, 20) -->
        <g transform="translate(128, 20) rotate(18)">
            <rect x="-4" y="2" width="8" height="1.8" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="0" y1="2" x2="0" y2="-5.5" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.3" />
            <circle cx="0" cy="-6.5" r="3.8" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.4" />
        </g>

        <!-- Guide 5 at (94, 38) -->
        <g transform="translate(94, 38) rotate(48)">
            <rect x="-3" y="2" width="6" height="1.8" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="0" y1="2" x2="0" y2="-5" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.2" />
            <circle cx="0" cy="-5.8" r="3.3" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.3" />
        </g>

        <!-- Guide 6 at (74, 68) -->
        <g transform="translate(74, 68) rotate(80)">
            <rect x="-3" y="2" width="6" height="1.8" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="0" y1="2" x2="0" y2="-4.2" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.1" />
            <circle cx="0" cy="-5" r="2.9" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.2" />
        </g>

        <!-- Tip-Top Guide at (68, 108) -->
        <g transform="translate(68, 108) rotate(105)">
            <rect x="-2" y="-1" width="4" height="5" rx="1" fill="#64748b" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.35" stroke-width="0.8" />
            <circle cx="0" cy="-3.5" r="2.6" fill="#ffffff" fill-opacity="0.5" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.3" />
        </g>

        <!-- Foregrip Cork -->
        <g transform="translate(305, 98) rotate(-45)">
            <rect x="-18" y="-5" width="22" height="10" rx="3" fill="url(#corkGripGrad)" stroke="#64748b" stroke-opacity="0.3" stroke-width="1.4" />
            <line x1="-12" y1="-5" x2="-12" y2="5" stroke="#64748b" stroke-opacity="0.2" stroke-width="1" />
            <line x1="-6" y1="-5" x2="-6" y2="5" stroke="#64748b" stroke-opacity="0.2" stroke-width="1" />
        </g>

        <!-- Reel Seat & Spinning Reel -->
        <g transform="translate(332, 125) rotate(-45)">
            <rect x="-16" y="-6" width="32" height="12" rx="2" fill="#64748b" fill-opacity="0.2" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.4" />
            <line x1="-12" y1="-6" x2="-12" y2="6" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.6" />
            <line x1="12" y1="-6" x2="12" y2="6" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.6" />

            <!-- Reel Stem -->
            <path d="M -4 6 L -8 18 L 8 18 L 4 6 Z" fill="url(#reelMetalGrad)" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.4" stroke-linejoin="round" />
            
            <!-- Reel Rotor & Spool Assembly -->
            <g transform="translate(0, 22)">
                <rect x="-18" y="-4" width="22" height="18" rx="4" fill="url(#reelMetalGrad)" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.5" />
                <line x1="-18" y1="-4" x2="-18" y2="14" stroke="#64748b" stroke-opacity="0.38" stroke-width="2" stroke-linecap="round" />
                <line x1="-14" y1="-2" x2="-14" y2="12" stroke="#64748b" stroke-opacity="0.22" stroke-width="0.9" />
                <line x1="-10" y1="-2" x2="-10" y2="12" stroke="#64748b" stroke-opacity="0.22" stroke-width="0.9" />
                <line x1="-6" y1="-2" x2="-6" y2="12" stroke="#64748b" stroke-opacity="0.22" stroke-width="0.9" />
                <line x1="-2" y1="-2" x2="-2" y2="12" stroke="#64748b" stroke-opacity="0.22" stroke-width="0.9" />
                <path d="M -18 5 C -24 -2, -10 -10, 4 -2" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.5" fill="none" stroke-linecap="round" />
                <circle cx="-18" cy="5" r="2.2" fill="#64748b" fill-opacity="0.38" />
                <path d="M 4 -2 C 18 -4, 26 8, 16 20 C 8 26, -2 22, 2 12 Z" fill="url(#reelMetalGrad)" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.5" stroke-linejoin="round" />
                <path d="M 12 10 L 22 18 L 22 28" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                <rect x="18" y="28" width="8" height="12" rx="4" fill="url(#corkGripGrad)" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.3" />
            </g>
        </g>

        <!-- Cork Rear Split Grip & Butt Cap -->
        <g transform="translate(365, 158) rotate(-45)">
            <rect x="-22" y="-5" width="28" height="10" rx="3" fill="url(#corkGripGrad)" stroke="#64748b" stroke-opacity="0.3" stroke-width="1.4" />
            <line x1="-16" y1="-5" x2="-16" y2="5" stroke="#64748b" stroke-opacity="0.18" stroke-width="1" />
            <line x1="-10" y1="-5" x2="-10" y2="5" stroke="#64748b" stroke-opacity="0.18" stroke-width="1" />
            <line x1="-4" y1="-5" x2="-4" y2="5" stroke="#64748b" stroke-opacity="0.18" stroke-width="1" />
            <rect x="6" y="-6" width="6" height="12" rx="2.5" fill="#64748b" fill-opacity="0.3" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.3" />
        </g>
    </svg>
</div>
