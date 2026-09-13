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

        <!-- SUBTLE WATER RIPPLES AT WATER ENTRY (Bottom-Left) -->
        <g opacity="0.75">
            <ellipse cx="28" cy="166" rx="26" ry="4.5" stroke="#64748b" stroke-opacity="0.2" stroke-width="1.2" stroke-dasharray="5 3" fill="#ffffff" fill-opacity="0.15" />
            <ellipse cx="28" cy="166" rx="14" ry="2.5" stroke="#64748b" stroke-opacity="0.25" stroke-width="1" fill="none" />
            <path d="M 22 162 Q 26 156 30 162" stroke="#64748b" stroke-opacity="0.22" stroke-width="1" fill="none" />
        </g>

        <!-- TAUT FISHING LINE (Shooting diagonally across the card from rod tip to bottom-left) -->
        <!-- Main heavy tension line from tip (220, 56) to water (28, 166) -->
        <line x1="220" y1="56" x2="28" y2="166" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.8" stroke-linecap="round" />
        <line x1="220.5" y1="56" x2="28.5" y2="166" stroke="#ffffff" stroke-opacity="0.45" stroke-width="1" stroke-linecap="round" />
        
        <!-- Vibration / Drag spray dashes alongside line -->
        <line x1="212" y1="62" x2="38" y2="162" stroke="#64748b" stroke-opacity="0.16" stroke-width="1" stroke-dasharray="6 8" />

        <!-- FISHING LINE THROUGH GUIDES (From Reel Spool through Guides to Tip) -->
        <path d="
            M 276 138 
            L 298 78 
            L 302 44 
            L 288 18 
            L 264 20 
            L 240 36 
            L 220 56
        " stroke="#64748b" stroke-opacity="0.26" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" fill="none" />

        <!-- LOADED ROD BLANK (Hooked parabolic curve on the right) -->
        <!-- Main spine stroke -->
        <path d="
            M 292 176 
            L 296 112 
            C 298 62, 316 20, 286 12 
            C 260 6, 235 28, 220 56
        " stroke="#64748b" stroke-opacity="0.34" stroke-width="4" stroke-linecap="round" fill="none" />
        
        <!-- Core highlight line -->
        <path d="
            M 292 176 
            L 296 112 
            C 298 62, 316 20, 286 12 
            C 260 6, 235 28, 220 56
        " stroke="#ffffff" stroke-opacity="0.45" stroke-width="1.8" stroke-linecap="round" fill="none" />

        <!-- ROD GUIDES & GUIDE WRAPS (Positioned along the blank curve) -->
        <!-- Stripper Guide at (297, 78) -->
        <g transform="translate(297, 78) rotate(6)">
            <rect x="-6" y="-1" width="12" height="2.5" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="-3.5" y1="0" x2="-8" y2="0" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.4" />
            <line x1="3.5" y1="0" x2="-8" y2="0" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.4" />
            <circle cx="-10" cy="0" r="6" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.8" />
        </g>

        <!-- Guide 2 at (302, 44) -->
        <g transform="translate(302, 44) rotate(15)">
            <rect x="-5" y="-1" width="10" height="2" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="0" y1="0" x2="-6" y2="0" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.3" />
            <circle cx="-8" cy="0" r="4.8" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.6" />
        </g>

        <!-- Guide 3 at (288, 16) -->
        <g transform="translate(288, 16) rotate(65)">
            <rect x="-4" y="-1" width="8" height="2" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="0" y1="0" x2="-5" y2="0" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.2" />
            <circle cx="-7" cy="0" r="4.2" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.5" />
        </g>

        <!-- Guide 4 at (260, 18) -->
        <g transform="translate(260, 18) rotate(110)">
            <rect x="-4" y="-1" width="8" height="1.8" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="0" y1="0" x2="-4.5" y2="0" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.2" />
            <circle cx="-6.2" cy="0" r="3.6" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.4" />
        </g>

        <!-- Guide 5 at (238, 36) -->
        <g transform="translate(238, 36) rotate(140)">
            <rect x="-3" y="-1" width="6" height="1.8" rx="1" fill="#64748b" fill-opacity="0.35" />
            <line x1="0" y1="0" x2="-4" y2="0" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.1" />
            <circle cx="-5.5" cy="0" r="3.2" fill="#ffffff" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.38" stroke-width="1.3" />
        </g>

        <!-- Tip-Top Guide at (220, 56) -->
        <g transform="translate(220, 56) rotate(152)">
            <rect x="-1" y="-1.5" width="4" height="3" rx="0.8" fill="#64748b" fill-opacity="0.4" stroke="#64748b" stroke-opacity="0.35" stroke-width="0.8" />
            <circle cx="-4" cy="0" r="2.6" fill="#ffffff" fill-opacity="0.5" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.3" />
        </g>

        <!-- Foregrip Cork -->
        <g transform="translate(295, 114) rotate(86)">
            <rect x="-14" y="-5" width="18" height="10" rx="3" fill="url(#corkGripGrad)" stroke="#64748b" stroke-opacity="0.3" stroke-width="1.4" />
            <line x1="-9" y1="-5" x2="-9" y2="5" stroke="#64748b" stroke-opacity="0.2" stroke-width="1" />
            <line x1="-4" y1="-5" x2="-4" y2="5" stroke="#64748b" stroke-opacity="0.2" stroke-width="1" />
        </g>

        <!-- Reel Seat & Spinning Reel (Mounted facing forward-left) -->
        <g transform="translate(294, 138)">
            <!-- Reel Seat Body -->
            <rect x="-5" y="-14" width="10" height="28" rx="2" fill="#64748b" fill-opacity="0.2" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.4" />
            <line x1="-5" y1="-10" x2="5" y2="-10" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.6" />
            <line x1="-5" y1="10" x2="5" y2="10" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.6" />

            <!-- Reel Stem -->
            <path d="M -5 -2 L -18 -6 L -18 6 L -5 2 Z" fill="url(#reelMetalGrad)" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.4" stroke-linejoin="round" />
            
            <!-- Reel Assembly -->
            <g transform="translate(-18, 0)">
                <!-- Rotor / Spool (angled up toward stripper guide) -->
                <g transform="rotate(15)">
                    <rect x="-16" y="-10" width="18" height="20" rx="4" fill="url(#reelMetalGrad)" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.5" />
                    <line x1="-16" y1="-10" x2="-16" y2="10" stroke="#64748b" stroke-opacity="0.38" stroke-width="2" stroke-linecap="round" />
                    <!-- Spooled Line -->
                    <line x1="-12" y1="-8" x2="-12" y2="8" stroke="#64748b" stroke-opacity="0.22" stroke-width="0.9" />
                    <line x1="-8" y1="-8" x2="-8" y2="8" stroke="#64748b" stroke-opacity="0.22" stroke-width="0.9" />
                    <line x1="-4" y1="-8" x2="-4" y2="8" stroke="#64748b" stroke-opacity="0.22" stroke-width="0.9" />
                    <!-- Bail Wire -->
                    <path d="M -16 -6 C -24 0, -20 12, -4 10" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.4" fill="none" stroke-linecap="round" />
                    <circle cx="-16" cy="-6" r="2" fill="#64748b" fill-opacity="0.38" />
                </g>

                <!-- Gearbox housing & handle -->
                <path d="M 0 -2 C 10 -4, 16 6, 8 16 C 2 20, -4 16, 0 6 Z" fill="url(#reelMetalGrad)" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.5" stroke-linejoin="round" />
                <path d="M 6 8 L 14 16 L 14 24" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                <rect x="11" y="24" width="6" height="10" rx="3" fill="url(#corkGripGrad)" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.2" />
            </g>
        </g>

        <!-- Cork Rear Handle & Fighting Butt -->
        <g transform="translate(293, 164) rotate(88)">
            <rect x="-18" y="-5" width="22" height="10" rx="3" fill="url(#corkGripGrad)" stroke="#64748b" stroke-opacity="0.3" stroke-width="1.4" />
            <line x1="-13" y1="-5" x2="-13" y2="5" stroke="#64748b" stroke-opacity="0.18" stroke-width="1" />
            <line x1="-8" y1="-5" x2="-8" y2="5" stroke="#64748b" stroke-opacity="0.18" stroke-width="1" />
            <rect x="4" y="-6" width="5" height="12" rx="2" fill="#64748b" fill-opacity="0.3" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.2" />
        </g>
    </svg>
</div>
