@props([
    'category' => 'all',
    'class' => 'w-7 h-7',
    'active' => false,
])

@php
    $normalized = strtolower(trim((string) $category));
    $strokeColor = $active ? 'currentColor' : 'currentColor';
@endphp

@if ($normalized === 'all' || $normalized === 'all trays')
    <!-- Layered Tackle Tray / Master Tackle Box Vector -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <rect x="4" y="6" width="24" height="6" rx="2" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.5"/>
        <line x1="12" y1="6" x2="12" y2="12" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-500' }}" stroke-width="1.5"/>
        <line x1="20" y1="6" x2="20" y2="12" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-500' }}" stroke-width="1.5"/>
        <rect x="4" y="14" width="24" height="6" rx="2" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.5"/>
        <line x1="16" y1="14" x2="16" y2="20" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-500' }}" stroke-width="1.5"/>
        <rect x="4" y="22" width="24" height="6" rx="2" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.5"/>
        <line x1="10" y1="22" x2="10" y2="28" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-500' }}" stroke-width="1.5"/>
        <line x1="22" y1="22" x2="22" y2="28" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-500' }}" stroke-width="1.5"/>
    </svg>

@elseif ($normalized === 'crankbait')
    <!-- Crankbait Silhouette with Diving Lip & Trebles -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <!-- Diving Lip -->
        <path d="M5 18L10 14" class="{{ $active ? 'stroke-cyan-300' : 'stroke-slate-400' }}" stroke-width="2" stroke-linecap="round"/>
        <!-- Body -->
        <path d="M8 14C8 9.5 13 8 18 9C23 10 26 13 27 15C26 16.5 22 18.5 17 18C12 17.5 9 16 8 14Z" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.5" stroke-linejoin="round"/>
        <!-- Eye -->
        <circle cx="11.5" cy="12.5" r="1.5" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-300' }}"/>
        <!-- Belly Treble Hook -->
        <path d="M15 18V22C15 24 13 25 12 24" class="{{ $active ? 'stroke-teal-400' : 'stroke-slate-500' }}" stroke-width="1.3" stroke-linecap="round"/>
        <!-- Tail Treble Hook -->
        <path d="M27 15L29 18C30 19.5 28 21 27 20" class="{{ $active ? 'stroke-teal-400' : 'stroke-slate-500' }}" stroke-width="1.3" stroke-linecap="round"/>
    </svg>

@elseif ($normalized === 'soft plastic')
    <!-- Ribbed Paddletail Soft Plastic Swimbait / Worm -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <!-- Tapered Ribbed Body -->
        <path d="M5 16C5 13.5 8 12.5 13 13C18 13.5 22 15 25 15.5C26 15.5 27 15 28 12.5C29 10 30 15 29 18C28 21 27 18 25 17.5C21 17 17 18 12 18.5C7 19 5 18 5 16Z" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.5" stroke-linejoin="round"/>
        <!-- Ribbed Texture Marks -->
        <line x1="9" y1="14" x2="9" y2="18" class="{{ $active ? 'stroke-teal-300/80' : 'stroke-slate-500' }}" stroke-width="1.2" stroke-linecap="round"/>
        <line x1="13" y1="14" x2="13" y2="18" class="{{ $active ? 'stroke-teal-300/80' : 'stroke-slate-500' }}" stroke-width="1.2" stroke-linecap="round"/>
        <line x1="17" y1="14.5" x2="17" y2="17.5" class="{{ $active ? 'stroke-teal-300/80' : 'stroke-slate-500' }}" stroke-width="1.2" stroke-linecap="round"/>
        <line x1="21" y1="15" x2="21" y2="17" class="{{ $active ? 'stroke-teal-300/80' : 'stroke-slate-500' }}" stroke-width="1.2" stroke-linecap="round"/>
        <!-- Eye -->
        <circle cx="7" cy="15" r="1" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-300' }}"/>
    </svg>

@elseif ($normalized === 'swimbait')
    <!-- Jointed Multi-Section Glide / Swimbait -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <!-- Head Section -->
        <path d="M4 16C4 12 7 11 11 11.5V20.5C7 21 4 20 4 16Z" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.4" stroke-linejoin="round"/>
        <!-- Mid Joint -->
        <path d="M12.5 11.7C15 12 17 12.5 19 13V19C17 19.5 15 20 12.5 20.3V11.7Z" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.4" stroke-linejoin="round"/>
        <!-- Tail Joint & Fin -->
        <path d="M20.5 13.5C22 14 24 14.5 25 15L29 11V21L25 17C24 17.5 22 18 20.5 18.5V13.5Z" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.4" stroke-linejoin="round"/>
        <!-- Eye -->
        <circle cx="7" cy="14.5" r="1.2" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-300' }}"/>
        <!-- Dorsal Fin -->
        <path d="M10 11.5L13 8L15 12" class="{{ $active ? 'stroke-teal-400' : 'stroke-slate-500' }}" stroke-width="1.2" stroke-linecap="round"/>
    </svg>

@elseif ($normalized === 'inline spinner')
    <!-- Inline Spinner with Spinning Blade & Dressed Treble -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <!-- Wire Shaft -->
        <line x1="4" y1="16" x2="23" y2="16" class="{{ $active ? 'stroke-cyan-300' : 'stroke-slate-400' }}" stroke-width="1.5" stroke-linecap="round"/>
        <!-- Tie Eyelet -->
        <circle cx="4" cy="16" r="1.5" class="{{ $active ? 'stroke-cyan-300' : 'stroke-slate-400' }}" stroke-width="1.2"/>
        <!-- Teardrop Spinner Blade -->
        <path d="M10 16C10 11 13 8 16 10C18 11.5 17 16 12 16" class="{{ $active ? 'fill-teal-400/40 stroke-teal-300' : 'fill-slate-700 stroke-slate-400' }}" stroke-width="1.4" stroke-linejoin="round"/>
        <!-- Solid Brass Weight Beads -->
        <circle cx="16" cy="16" r="2" class="{{ $active ? 'fill-amber-400 stroke-amber-300' : 'fill-slate-600 stroke-slate-400' }}" stroke-width="1"/>
        <circle cx="20" cy="16" r="2.2" class="{{ $active ? 'fill-amber-400 stroke-amber-300' : 'fill-slate-600 stroke-slate-400' }}" stroke-width="1"/>
        <!-- Feathered Treble Hook -->
        <path d="M23 16L27 13M23 16L28 16M23 16L27 19" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-400' }}" stroke-width="1.5" stroke-linecap="round"/>
    </svg>

@elseif ($normalized === 'spinnerbait')
    <!-- Overhead Arm Spinnerbait with Dual Blades & Skirt -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <!-- V-Wire Frame -->
        <path d="M6 16L12 8L20 8" class="{{ $active ? 'stroke-cyan-300' : 'stroke-slate-400' }}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <line x1="6" y1="16" x2="15" y2="22" class="{{ $active ? 'stroke-cyan-300' : 'stroke-slate-400' }}" stroke-width="1.5" stroke-linecap="round"/>
        <!-- Willow Leaf Blade 1 -->
        <path d="M15 8C17 6 20 6 22 8C20 10 17 10 15 8Z" class="{{ $active ? 'fill-amber-400/40 stroke-amber-300' : 'fill-slate-700 stroke-slate-400' }}" stroke-width="1.2"/>
        <!-- Willow Leaf Blade 2 -->
        <path d="M21 8C23 5.5 26 5.5 28 8C26 10.5 23 10.5 21 8Z" class="{{ $active ? 'fill-amber-400/50 stroke-amber-300' : 'fill-slate-700 stroke-slate-400' }}" stroke-width="1.2"/>
        <!-- Leadhead & Hook -->
        <circle cx="15" cy="22" r="3" class="{{ $active ? 'fill-teal-400 stroke-teal-300' : 'fill-slate-600 stroke-slate-400' }}" stroke-width="1.2"/>
        <!-- Silicone Skirt Tendrils -->
        <path d="M16 22C19 21 22 20 26 21M16 23C19 24 23 25 27 24M16 22C20 23 24 22 28 23" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-500' }}" stroke-width="1.3" stroke-linecap="round"/>
    </svg>

@elseif ($normalized === 'jig')
    <!-- Weedless Bass / Football Jig with Hook & Skirt -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <!-- Lead Jig Head -->
        <circle cx="10" cy="16" r="4.5" class="{{ $active ? 'fill-teal-400/40 stroke-teal-300' : 'fill-slate-700 stroke-slate-400' }}" stroke-width="1.5"/>
        <circle cx="9" cy="15" r="1.2" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-300' }}"/>
        <!-- Fiber Weedguard -->
        <line x1="11" y1="12" x2="19" y2="12" class="{{ $active ? 'stroke-slate-300' : 'stroke-slate-500' }}" stroke-width="1.5" stroke-linecap="round"/>
        <!-- Heavy Gauge Jig Hook -->
        <path d="M14 16H21C24 16 25 13 23 11" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-400' }}" stroke-width="1.6" stroke-linecap="round"/>
        <!-- Silicone Skirt Flare -->
        <path d="M12 16C15 18 19 20 24 21M12 17C16 19 18 22 22 24M12 16C15 17 18 18 22 18" class="{{ $active ? 'stroke-teal-400' : 'stroke-slate-500' }}" stroke-width="1.3" stroke-linecap="round"/>
    </svg>

@elseif ($normalized === 'spoon')
    <!-- Curved Dimpled Casting Spoon -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <!-- Split Ring Top -->
        <circle cx="6" cy="16" r="1.5" class="{{ $active ? 'stroke-cyan-300' : 'stroke-slate-400' }}" stroke-width="1.2"/>
        <!-- Curved Metallic Spoon Body -->
        <path d="M8 16C9 12 15 10 21 11C24 11.5 25 14 24 16C23 18 20 21 14 20.5C9 20 8 18 8 16Z" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.5" stroke-linejoin="round"/>
        <!-- Dimpled Texture Highlights -->
        <circle cx="13" cy="15" r="0.8" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-400' }}"/>
        <circle cx="17" cy="14" r="0.8" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-400' }}"/>
        <circle cx="16" cy="17" r="0.8" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-400' }}"/>
        <circle cx="20" cy="16" r="0.8" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-400' }}"/>
        <!-- Split Ring Bottom & Treble -->
        <path d="M24 16L27 18C28 19 27 21 25 21" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-400' }}" stroke-width="1.3" stroke-linecap="round"/>
    </svg>

@elseif ($normalized === 'topwater')
    <!-- Surface Popper / Walker with Cupped Mouth & Feather -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <!-- Water Surface Line -->
        <line x1="3" y1="13" x2="29" y2="13" class="{{ $active ? 'stroke-cyan-400/40' : 'stroke-slate-600/40' }}" stroke-width="1" stroke-dasharray="2 2"/>
        <!-- Cupped Face Popper Body -->
        <path d="M6 14C6 14 7 17 11 17C16 17 21 16 25 15C26 14.8 26 13.5 25 13.2C20 12 14 12 9 12.5C7 12.8 6 14 6 14Z" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.5" stroke-linejoin="round"/>
        <!-- Concave Mouth Notch -->
        <path d="M6 13C7 13.5 7 14.5 6 15" class="{{ $active ? 'stroke-cyan-300' : 'stroke-slate-400' }}" stroke-width="1.5"/>
        <!-- Eye -->
        <circle cx="9.5" cy="14" r="1.2" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-300' }}"/>
        <!-- Feathered Tail Hook -->
        <path d="M25 15L29 18M25 15L29 15M25 15L28 13" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-400' }}" stroke-width="1.3" stroke-linecap="round"/>
        <!-- Splash Droplets -->
        <circle cx="5" cy="11" r="0.8" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-400' }}"/>
        <circle cx="7" cy="9" r="0.6" class="{{ $active ? 'fill-cyan-300' : 'fill-slate-400' }}"/>
    </svg>

@elseif ($normalized === 'fly')
    <!-- Streamer / Bugger Fly with Hackle & Marabou -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <!-- Thread Head & Eyelet -->
        <circle cx="6" cy="15" r="1.5" class="{{ $active ? 'stroke-cyan-300' : 'stroke-slate-400' }}" stroke-width="1.2"/>
        <circle cx="8" cy="15" r="2" class="{{ $active ? 'fill-amber-400 stroke-amber-300' : 'fill-slate-600 stroke-slate-400' }}" stroke-width="1"/>
        <!-- Hook Shank -->
        <line x1="8" y1="15" x2="18" y2="15" class="{{ $active ? 'stroke-slate-300' : 'stroke-slate-500' }}" stroke-width="1.5"/>
        <!-- Hook Bend -->
        <path d="M18 15C21 15 22 18 20 20C18 21 16 19 16 18" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-400' }}" stroke-width="1.4" stroke-linecap="round"/>
        <!-- Hackle Fibers -->
        <path d="M10 15L8 10M12 15L10 9M14 15L12 10M10 15L8 20M12 15L10 21" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-400' }}" stroke-width="1.2" stroke-linecap="round"/>
        <!-- Flowing Marabou Tail -->
        <path d="M18 15C21 13 25 12 28 13C25 15 26 17 28 18C24 17 21 16 18 15Z" class="{{ $active ? 'fill-teal-400/40 stroke-teal-300' : 'fill-slate-700 stroke-slate-400' }}" stroke-width="1.2" stroke-linejoin="round"/>
    </svg>

@else
    <!-- Other / Terminal Tackle -->
    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
        <rect x="7" y="7" width="18" height="18" rx="3" class="{{ $active ? 'fill-teal-400/30 stroke-teal-300' : 'fill-slate-800 stroke-slate-400' }}" stroke-width="1.5"/>
        <path d="M7 16H25M16 7V25" class="{{ $active ? 'stroke-teal-300' : 'stroke-slate-500' }}" stroke-width="1.3"/>
        <circle cx="16" cy="16" r="3" class="{{ $active ? 'fill-cyan-300/40 stroke-cyan-300' : 'fill-slate-700 stroke-slate-400' }}" stroke-width="1.2"/>
    </svg>
@endif
