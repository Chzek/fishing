@props([
    'class' => 'absolute inset-0 w-full h-full pointer-events-none select-none overflow-hidden',
])

<div {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
    <svg viewBox="0 0 400 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full" preserveAspectRatio="xMidYMid slice">
        <!-- Lake Surface Ripple (Bottom-Right Under Pour Stream) -->
        <ellipse cx="260" cy="166" rx="28" ry="4.5" stroke="#64748b" stroke-opacity="0.25" stroke-width="1.2" stroke-dasharray="5 3" />
        <ellipse cx="260" cy="166" rx="14" ry="2.2" stroke="#64748b" stroke-opacity="0.32" stroke-width="1.2" />

        <!-- Clean Line-Art Pour Stream -->
        <path d="M 270 102 C 267 122, 258 145, 257 166" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.8" stroke-linecap="round" fill="none" />
        <path d="M 276 105 C 273 125, 264 148, 263 166" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.8" stroke-linecap="round" fill="none" />

        <!-- Minimalist Line-Art Tilted Beer Can (Clean contours matching user sketch, zero shading) -->
        <g transform="translate(325, 78) rotate(-38)">
            <!-- Top Outer Lid Rim -->
            <ellipse cx="0" cy="-38" rx="22" ry="8" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.8" />
            <!-- Inner Lid Rim Lip -->
            <ellipse cx="0" cy="-38" rx="18.5" ry="6.5" stroke="#64748b" stroke-opacity="0.3" stroke-width="1.2" />

            <!-- Open Pouring Hole -->
            <ellipse cx="-5" cy="-39" rx="6" ry="3.2" fill="#64748b" fill-opacity="0.45" stroke="#64748b" stroke-opacity="0.45" stroke-width="1" />

            <!-- Pop-Tab -->
            <g transform="translate(4, -40) rotate(-30)">
                <path d="M -4 -8 C -4 -10, 4 -10, 4 -8 L 3 2 C 3 3, -3 3, -3 2 Z" stroke="#64748b" stroke-opacity="0.45" stroke-width="1.2" fill="none" />
                <circle cx="0" cy="-5" r="1.8" stroke="#64748b" stroke-opacity="0.35" stroke-width="1" />
                <circle cx="0" cy="0" r="1.2" fill="#64748b" fill-opacity="0.35" />
            </g>

            <!-- Neck Taper Lines -->
            <path d="M -22 -38 L -25 -26" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.8" />
            <path d="M 22 -38 L 25 -26" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.8" />

            <!-- Shoulder Chime Curve -->
            <path d="M -25 -26 C -18 -21, 18 -21, 25 -26" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.5" fill="none" />

            <!-- Can Body Outer Cylinder -->
            <line x1="-25" y1="-26" x2="-25" y2="40" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.8" />
            <line x1="25" y1="-26" x2="25" y2="40" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.8" />

            <!-- Bottom Chime Curve -->
            <path d="M -25 40 C -18 45, 18 45, 25 40" stroke="#64748b" stroke-opacity="0.35" stroke-width="1.5" fill="none" />

            <!-- Bottom Base Rim -->
            <path d="M -25 40 C -25 46, -20 49, -15 50 C -5 51.5, 5 51.5, 15 50 C 20 49, 25 46, 25 40" stroke="#64748b" stroke-opacity="0.4" stroke-width="1.8" fill="none" />

            <!-- "BLUE" Label -->
            <text x="0" y="10" font-size="14" font-weight="900" font-style="italic" font-family="'Plus Jakarta Sans', system-ui, -apple-system, sans-serif" letter-spacing="1.5" fill="#64748b" fill-opacity="0.42" text-anchor="middle">BLUE</text>
        </g>
    </svg>
</div>
