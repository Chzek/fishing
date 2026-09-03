@props([
    'category' => 'all',
    'class' => 'w-8 h-8',
    'active' => false,
])

@php
    $normalized = strtolower(trim((string) $category));
    $colorClass = $active ? 'text-cyan-300' : 'text-slate-300 group-hover:text-white';
@endphp

@if ($normalized === 'all' || $normalized === 'all trays')
    <!-- Master Tackle Tray Organizer (3-Tier Layered Drawers) -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- Top Drawer Tier -->
        <rect x="4" y="5.5" width="28" height="7" rx="2" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="{{ $active ? '0.2' : '0.1' }}"/>
        <line x1="13" y1="5.5" x2="13" y2="12.5" stroke="currentColor" stroke-width="1.8" opacity="0.7"/>
        <line x1="23" y1="5.5" x2="23" y2="12.5" stroke="currentColor" stroke-width="1.8" opacity="0.7"/>
        
        <!-- Mid Drawer Tier -->
        <rect x="4" y="14.5" width="28" height="7" rx="2" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="{{ $active ? '0.2' : '0.1' }}"/>
        <line x1="18" y1="14.5" x2="18" y2="21.5" stroke="currentColor" stroke-width="1.8" opacity="0.7"/>
        
        <!-- Bottom Deep Tray Tier -->
        <rect x="4" y="23.5" width="28" height="7" rx="2" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="{{ $active ? '0.2' : '0.1' }}"/>
        <line x1="11" y1="23.5" x2="11" y2="30.5" stroke="currentColor" stroke-width="1.8" opacity="0.7"/>
        <line x1="25" y1="23.5" x2="25" y2="30.5" stroke="currentColor" stroke-width="1.8" opacity="0.7"/>
    </svg>

@elseif ($normalized === 'crankbait')
    <!-- Deep Diving Crankbait with Lip & Treble Hooks -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- Angled Diving Bill Lip -->
        <path d="M4 21L11 16" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
        <line x1="6" y1="19.5" x2="9" y2="17.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
        
        <!-- Arched Crankbait Body -->
        <path d="M9 16C9 10.5 15 8.5 21 9.5C26.5 10.5 30 14 31 16.5C30 18.5 25.5 21 19.5 20.5C13.5 20 10 18 9 16Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        
        <!-- Lateral Scale Texture Arcs -->
        <path d="M16 11.5C18 13.5 18 16.5 16 18.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
        <path d="M21 12C23 13.5 23 16 21 17.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
        
        <!-- 3D Eye -->
        <circle cx="13" cy="14" r="1.8" fill="currentColor"/>
        
        <!-- Belly Treble Hook Attachment & Bend -->
        <path d="M17 20.5V25C17 27.5 14.5 28.5 13.5 27.5M17 25C17 27.5 19.5 28.5 20.5 27.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        
        <!-- Tail Treble Hook Attachment & Bend -->
        <path d="M31 16.5L33.5 20C34.5 21.5 32.5 23.5 31.5 22.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    </svg>

@elseif ($normalized === 'soft plastic')
    <!-- Ribbed Paddletail Soft Plastic Swimbait / Worm -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- Tapered Contoured Body -->
        <path d="M4 18C4 15 7.5 13.5 13.5 14C19.5 14.5 24.5 16.5 28 17C29.5 17 30.5 16 31.5 13C33 9.5 34.5 16.5 33.5 20.5C32.5 24 30.5 20 28 19.5C23.5 19 18.5 20.5 12.5 21C6.5 21.5 4 20.5 4 18Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        
        <!-- High-Density Ribbed Segments -->
        <line x1="9" y1="15.5" x2="9" y2="20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
        <line x1="13" y1="15.5" x2="13" y2="20.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
        <line x1="17" y1="16" x2="17" y2="20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
        <line x1="21" y1="16.5" x2="21" y2="19.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
        <line x1="25" y1="17" x2="25" y2="19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
        
        <!-- Eye -->
        <circle cx="6.5" cy="17" r="1.5" fill="currentColor"/>
    </svg>

@elseif ($normalized === 'swimbait')
    <!-- Jointed Multi-Section Glide Swimbait -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- Section 1: Head & Pectoral Fin -->
        <path d="M4 18C4 13 8 11.5 12.5 12V24C8 24.5 4 23 4 18Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        
        <!-- Section 2: Mid Segment -->
        <path d="M14.5 12.5C17.5 13 20 13.5 22 14.5V21.5C20 22.5 17.5 23 14.5 23.5V12.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        
        <!-- Section 3: Tail Joint & Caudal Fin -->
        <path d="M24 15C25.5 15.5 27.5 16 28.5 16.5L33 11.5V24.5L28.5 19.5C27.5 20 25.5 20.5 24 21V15Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        
        <!-- Dorsal Fin Profile -->
        <path d="M11 12L15 7.5L17.5 13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.75"/>
        
        <!-- Eye -->
        <circle cx="7.5" cy="16" r="1.5" fill="currentColor"/>
    </svg>

@elseif ($normalized === 'inline spinner')
    <!-- Inline Spinner with Willow Blade & Dressed Treble -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- Main Stainless Wire Shaft -->
        <line x1="3" y1="18" x2="26" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <circle cx="3.5" cy="18" r="1.5" stroke="currentColor" stroke-width="1.6"/>
        
        <!-- Clevis Mounted Rotating Willow Blade -->
        <path d="M9 18C9 12 13 8 17 10.5C20 12.5 19 18 12 18" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        
        <!-- Machined Brass Weight Beads -->
        <circle cx="18" cy="18" r="2.2" fill="currentColor"/>
        <circle cx="23" cy="18" r="2.5" fill="currentColor"/>
        
        <!-- Feather-Dressed Treble Hook -->
        <path d="M26 18L31 14.5M26 18L33 18M26 18L31 21.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>

@elseif ($normalized === 'spinnerbait')
    <!-- Overhead Tandem Blade Spinnerbait with Skirt -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- V-Wire Overhead Frame -->
        <path d="M6 18L13 8.5L23 8.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <line x1="6" y1="18" x2="16.5" y2="25" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        
        <!-- Tandem Willow Blade 1 -->
        <path d="M16 8.5C18.5 6 22 6 24 8.5C22 11 18.5 11 16 8.5Z" stroke="currentColor" stroke-width="1.6" fill="currentColor" fill-opacity="{{ $active ? '0.3' : '0.15' }}"/>
        <!-- Tandem Willow Blade 2 -->
        <path d="M24 8.5C26.5 5.5 30.5 5.5 32.5 8.5C30.5 11.5 26.5 11.5 24 8.5Z" stroke="currentColor" stroke-width="1.6" fill="currentColor" fill-opacity="{{ $active ? '0.3' : '0.15' }}"/>
        
        <!-- Sculpted Lead Jighead -->
        <circle cx="16.5" cy="25" r="3.5" fill="currentColor"/>
        
        <!-- Flared Silicone Skirt Strands -->
        <path d="M18 25C22 23.5 26 22 31 23.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
        <path d="M18 26C22 27 27 28.5 32 27.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
        <path d="M18 25C23 25.5 27 25 33 25.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
    </svg>

@elseif ($normalized === 'jig')
    <!-- Heavy Bass Leadhead Jig with Fiber Weedguard & Skirt -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- Round Leadhead -->
        <circle cx="11" cy="18" r="5" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        <circle cx="10" cy="17" r="1.6" fill="currentColor"/>
        
        <!-- Fiber Weedguard Bristles -->
        <line x1="12" y1="13.5" x2="21" y2="13.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
        
        <!-- Heavy Forged Hook Shank & Bend -->
        <path d="M16 18H24C27.5 18 28.5 14.5 26.5 12.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
        
        <!-- Silicone Skirt Flares -->
        <path d="M13 18C17 20.5 22 23 28 24M13 19C17 22 21 25.5 26 27.5M13 18C17 19.5 22 20.5 27 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
    </svg>

@elseif ($normalized === 'spoon')
    <!-- Curved Dimpled Casting Spoon -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- Split Ring Eyelet -->
        <circle cx="6" cy="18" r="1.8" stroke="currentColor" stroke-width="1.6"/>
        
        <!-- Hydrodynamic Metallic Spoon Profile -->
        <path d="M8.5 18C10 13 17 11 24 12C27.5 12.5 29 15.5 28 18C27 20.5 23.5 23.5 16 23C10 22.5 9 20 8.5 18Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        
        <!-- Dimpled Surface Reflection Accents -->
        <circle cx="14" cy="16.5" r="1.2" fill="currentColor"/>
        <circle cx="19" cy="15.5" r="1.2" fill="currentColor"/>
        <circle cx="18" cy="19" r="1.2" fill="currentColor"/>
        <circle cx="23" cy="18" r="1.2" fill="currentColor"/>
        
        <!-- Trailing Treble Hook -->
        <path d="M28 18L32 20.5C33 22 32 24 30 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
    </svg>

@elseif ($normalized === 'topwater')
    <!-- Surface Popper / Chugger with Cupped Mouth -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- Water Surface Level -->
        <line x1="3" y1="15" x2="33" y2="15" stroke="currentColor" stroke-width="1.5" stroke-dasharray="2 2" opacity="0.5"/>
        
        <!-- Cupped Face Surface Popper Body -->
        <path d="M7 16C7 16 8 19.5 13 19.5C18.5 19.5 24 18 28.5 17C29.5 16.8 29.5 15.2 28.5 15C23 13.5 16 13.5 10.5 14C8.5 14.2 7 16 7 16Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        
        <!-- Concave Chugging Mouth Notch -->
        <path d="M7 15C8 15.5 8 16.5 7 17" stroke="currentColor" stroke-width="2.2"/>
        
        <!-- Eye -->
        <circle cx="11" cy="16" r="1.6" fill="currentColor"/>
        
        <!-- Feathered Dressing & Trebles -->
        <path d="M28.5 17L33 20.5M28.5 17L33 17M28.5 17L32 14.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity="0.85"/>
    </svg>

@elseif ($normalized === 'fly')
    <!-- Tied Streamer / Woolly Bugger Fly with Hackle & Marabou -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <!-- Brass Beadhead & Eyelet -->
        <circle cx="7" cy="17" r="1.6" stroke="currentColor" stroke-width="1.6"/>
        <circle cx="9.5" cy="17" r="2.4" fill="currentColor"/>
        
        <!-- Hook Shank & Deep Bend -->
        <line x1="9.5" y1="17" x2="21" y2="17" stroke="currentColor" stroke-width="2"/>
        <path d="M21 17C24.5 17 25.5 20.5 23 23C21 24.5 18.5 22 18.5 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        
        <!-- Hackle Collars -->
        <path d="M12 17L9.5 11M14.5 17L12 10M17 17L14.5 11M12 17L9.5 23M14.5 17L12 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity="0.8"/>
        
        <!-- Flowing Marabou Tail -->
        <path d="M21 17C24.5 14.5 29 13.5 33 14.5C29.5 17 31 19.5 33 21C28.5 19.5 24.5 18.5 21 17Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
    </svg>

@else
    <!-- Other / Tactical Terminal Tackle -->
    <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }} {{ $colorClass }} transition-all duration-200 shrink-0">
        <rect x="7" y="7" width="22" height="22" rx="3.5" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="{{ $active ? '0.25' : '0.1' }}"/>
        <path d="M7 18H29M18 7V29" stroke="currentColor" stroke-width="1.8" opacity="0.75"/>
        <circle cx="18" cy="18" r="3.5" fill="currentColor"/>
    </svg>
@endif
