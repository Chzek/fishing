@props([
    'badgePivot' => null,
    'badge' => null,
    'size' => 'md',
])

@php
    /** @var \Fishinglog\Models\AnglerBadge|null $pivot */
    $pivot = $badgePivot;
    /** @var \Fishinglog\Models\Badge $badgeModel */
    $badgeModel = $badge ?? $pivot?->badge;

    if (!$badgeModel) {
        return;
    }

    $tierBorderClass = $badgeModel->getTierBorderColor();
    $tierBadgeClass = $badgeModel->getTierBadgeClass();
    $awardedDate = $pivot?->awarded_at ? $pivot->awarded_at->format('M j, Y') : null;
    $triggerSummary = $pivot?->trigger_summary;
    $recordId = $pivot?->record_id;

    $dimClass = match($size) {
        'sm' => 'w-8 h-8',
        'lg' => 'w-14 h-14 md:w-16 md:h-16',
        'xl' => 'w-20 h-20 md:w-24 md:h-24',
        default => 'w-10 h-10 md:w-11 md:h-11',
    };
    $iconDimClass = match($size) {
        'sm' => 'w-4 h-4',
        'lg' => 'w-7 h-7',
        'xl' => 'w-10 h-10',
        default => 'w-5 h-5',
    };
    $pipDimClass = match($size) {
        'sm' => 'w-2.5 h-2.5',
        'lg' => 'w-3.5 h-3.5',
        'xl' => 'w-4 h-4',
        default => 'w-3 h-3',
    };
@endphp

<div x-data="{ open: false }" class="relative inline-block" @keydown.escape.window="open = false">
    <!-- Badge Patch Avatar / Trigger -->
    <button 
        type="button"
        @mouseenter="open = true" 
        @mouseleave="open = false" 
        @click="open = !open" 
        @focus="open = true"
        @blur="open = false"
        class="group relative block focus:outline-none focus:ring-2 focus:ring-teal-400 rounded-full transition-transform duration-200 hover:scale-110"
        aria-label="{{ $badgeModel->name }} Merit Badge"
    >
        @if($badgeModel->image_path && file_exists(public_path($badgeModel->image_path)))
            <img 
                src="{{ asset($badgeModel->image_path) }}" 
                alt="{{ $badgeModel->name }}" 
                class="{{ $dimClass }} rounded-full object-cover shadow-lg border-2 {{ $tierBorderClass }} ring-2 ring-black/50 group-hover:brightness-110 transition-all duration-200" 
            />
        @else
            <!-- Stitched Cloth / Twill Fallback with Merrowed Embroidered Edge -->
            <div class="{{ $dimClass }} rounded-full flex items-center justify-center border-2 border-dashed {{ $tierBorderClass }} bg-gradient-to-br from-slate-800 to-slate-900 shadow-lg ring-2 ring-black/50 group-hover:brightness-110 transition-all duration-200">
                <x-dynamic-component :component="'lucide-' . ($badgeModel->icon ?: 'award')" class="{{ $iconDimClass }} text-amber-300 drop-shadow" />
            </div>
        @endif

        <!-- Micro Tier Indicator Pip -->
        <span class="absolute -bottom-0.5 -right-0.5 {{ $pipDimClass }} rounded-full border border-black/80 {{ match(strtolower($badgeModel->tier)) {
            'platinum' => 'bg-cyan-400',
            'gold' => 'bg-amber-400',
            'silver' => 'bg-slate-300',
            default => 'bg-amber-700',
        } }}" title="{{ ucfirst($badgeModel->tier) }} Tier"></span>
    </button>

    <!-- Interactive Mouseover / Tap Popover Card -->
    <div 
        x-show="open" 
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        @click.outside="open = false"
        class="absolute z-50 bottom-full left-1/2 -translate-x-1/2 mb-2.5 w-72 bg-slate-950/95 backdrop-blur-md text-white rounded-xl p-3.5 shadow-2xl border border-slate-700/80 pointer-events-auto text-left"
    >
        <!-- Header: Title & Points Pill -->
        <div class="flex items-start justify-between gap-2 pb-2 border-b border-slate-800">
            <div class="min-w-0">
                <h4 class="text-sm font-extrabold text-white tracking-tight flex items-center gap-1.5 truncate">
                    <span>{{ $badgeModel->name }}</span>
                </h4>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $tierBadgeClass }}">
                        {{ $badgeModel->tier }}
                    </span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-teal-950 text-teal-300 border border-teal-700/60">
                        +{{ $badgeModel->points }} pts
                    </span>
                </div>
            </div>

            @if($badgeModel->image_path && file_exists(public_path($badgeModel->image_path)))
                <img src="{{ asset($badgeModel->image_path) }}" alt="" class="w-8 h-8 rounded-full border border-slate-700 shrink-0 object-cover" />
            @endif
        </div>

        <!-- Description -->
        <p class="text-xs text-slate-300 mt-2 leading-relaxed">
            {{ $badgeModel->description }}
        </p>

        <!-- Awarded Timestamp & Trigger Context -->
        <div class="mt-2.5 pt-2 border-t border-slate-800/80 space-y-1 text-[11px]">
            @if($awardedDate)
                <div class="flex items-center gap-1 text-slate-400">
                    <x-lucide-calendar class="w-3 h-3 text-slate-500 shrink-0" />
                    <span>Awarded {{ $awardedDate }}</span>
                </div>
            @endif

            @if($triggerSummary)
                <div class="text-slate-300 bg-slate-900/90 rounded-md p-1.5 border border-slate-800 text-[10.5px]">
                    <span class="text-teal-400 font-semibold block text-[10px] uppercase tracking-wide">Trigger Context:</span>
                    <span>{{ $triggerSummary }}</span>
                </div>
            @endif

            @if($recordId)
                <div class="pt-1">
                    <a href="{{ url('/record/' . $recordId) }}" class="inline-flex items-center gap-1 text-teal-400 hover:text-teal-300 font-semibold text-[11px] transition-colors">
                        <span>View Catch Record</span>
                        <x-lucide-arrow-right class="w-3 h-3" />
                    </a>
                </div>
            @endif
        </div>

        <!-- Caret / Arrow Pointing Down -->
        <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-slate-950 border-r border-b border-slate-700/80 transform rotate-45"></div>
    </div>
</div>
