@props([
    'angler',
    'earnedBadges' => null,
])

@php
    /** @var \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\AnglerBadge> $badges */
    $badges = $earnedBadges ?? ($angler->earnedBadges ?? collect());
    $earnedCount = $badges->count();
    $totalAvailable = 42;
    $totalPoints = $badges->sum(fn ($b) => $b->badge->points ?? 0);

    $platinumCount = $badges->filter(fn ($b) => strtolower($b->badge->tier ?? '') === 'platinum')->count();
    $goldCount = $badges->filter(fn ($b) => strtolower($b->badge->tier ?? '') === 'gold')->count();
    $silverCount = $badges->filter(fn ($b) => strtolower($b->badge->tier ?? '') === 'silver')->count();
    $bronzeCount = $badges->filter(fn ($b) => strtolower($b->badge->tier ?? '') === 'bronze')->count();

    // Group categories with counts
    $categoryLabels = [
        'volume' => 'Volume',
        'species' => 'Species',
        'diversity' => 'Diversity',
        'exploration' => 'Exploration',
        'weather' => 'Weather',
        'cadence' => 'Cadence',
        'streak' => 'Streak',
        'conservation' => 'Conservation',
        'yardage' => 'Yardage',
        'heritage' => 'Heritage',
        'nomad' => 'Nomad',
    ];

    $categoryCounts = [];
    foreach ($categoryLabels as $key => $label) {
        $count = $badges->filter(fn ($b) => ($b->badge->category ?? '') === $key)->count();
        if ($count > 0) {
            $categoryCounts[$key] = [
                'label' => $label,
                'count' => $count,
            ];
        }
    }
@endphp

@if($earnedCount > 0)
    <div 
        id="merit-badge-sash" 
        x-data="{ activeCategory: 'all' }" 
        x-show="typeof showSash !== 'undefined' ? showSash : true"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.99]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.99]"
        class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 space-y-5"
    >
        <!-- Header: Title, Telemetry & Tier Summary Chips -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-800">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center shrink-0 shadow-inner">
                    <x-lucide-award class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-white tracking-tight flex items-center gap-2">
                        <span>Angler Merit Badge Sash</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-teal-500/15 border border-teal-500/30 text-teal-300">
                            {{ $earnedCount }} of {{ $totalAvailable }} Earned
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Career achievement patches & scouting milestones across northern waters.
                    </p>
                </div>
            </div>

            <!-- Tier Trophy Pills & Close Button -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-300">
                    <span class="text-slate-400 text-[11px] uppercase tracking-wider font-semibold">Total Points:</span>
                    <span class="font-mono font-bold text-amber-300">{{ number_format($totalPoints) }}</span>
                </div>
                @if($platinumCount > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-cyan-950/80 border border-cyan-500/30 text-cyan-300 font-mono text-[11px] font-bold" title="Platinum Badges">
                        💎 {{ $platinumCount }}
                    </span>
                @endif
                @if($goldCount > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-950/80 border border-amber-500/30 text-amber-300 font-mono text-[11px] font-bold" title="Gold Badges">
                        🥇 {{ $goldCount }}
                    </span>
                @endif
                @if($silverCount > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-800 border border-slate-600/50 text-slate-200 font-mono text-[11px] font-bold" title="Silver Badges">
                        🥈 {{ $silverCount }}
                    </span>
                @endif
                @if($bronzeCount > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-950/40 border border-amber-800/40 text-amber-400 font-mono text-[11px] font-bold" title="Bronze Badges">
                        🥉 {{ $bronzeCount }}
                    </span>
                @endif

                <button 
                    type="button" 
                    x-show="typeof showSash !== 'undefined'"
                    @click="showSash = false" 
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-semibold transition-colors cursor-pointer"
                    title="Collapse Merit Badge Sash"
                >
                    <span>Hide Sash</span>
                    <x-lucide-chevron-up class="w-3.5 h-3.5 text-slate-400" />
                </button>
            </div>
        </div>

        <!-- Category Filter Pill Bar -->
        <div class="flex flex-wrap items-center gap-1.5">
            <button 
                type="button" 
                @click="activeCategory = 'all'"
                :class="activeCategory === 'all' ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60'"
                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all cursor-pointer inline-flex items-center gap-1.5"
            >
                <span>All Badges</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold" :class="activeCategory === 'all' ? 'bg-teal-700 text-teal-100' : 'bg-slate-900 text-slate-400'">
                    {{ $earnedCount }}
                </span>
            </button>

            @foreach($categoryCounts as $catKey => $catData)
                <button 
                    type="button" 
                    @click="activeCategory = '{{ $catKey }}'"
                    :class="activeCategory === '{{ $catKey }}' ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60'"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all cursor-pointer inline-flex items-center gap-1.5"
                >
                    <span>{{ $catData['label'] }}</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono font-bold" :class="activeCategory === '{{ $catKey }}' ? 'bg-teal-700 text-teal-100' : 'bg-slate-900 text-slate-400'">
                        {{ $catData['count'] }}
                    </span>
                </button>
            @endforeach
        </div>

        <!-- The Merit Badge Sash Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 pt-1">
            @foreach($badges as $badgePivot)
                <div 
                    x-show="activeCategory === 'all' || activeCategory === '{{ $badgePivot->badge->category }}'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-slate-950/60 hover:bg-slate-950 border border-slate-800 hover:border-slate-700 rounded-2xl p-4 flex flex-col items-center text-center transition-all duration-200 group relative shadow-sm"
                >
                    <!-- Embroidered Patch with Interactive Popover -->
                    <div class="my-1">
                        <x-meritBadge :badgePivot="$badgePivot" size="lg" />
                    </div>

                    <!-- Badge Name -->
                    <h4 class="font-bold text-xs text-white group-hover:text-teal-300 transition-colors line-clamp-1 w-full mt-2.5" title="{{ $badgePivot->badge->name }}">
                        {{ $badgePivot->badge->name }}
                    </h4>

                    <!-- Tier & Points Badges -->
                    <div class="flex items-center gap-1.5 mt-1.5">
                        <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded border {{ $badgePivot->badge->getTierBadgeClass() }}">
                            {{ $badgePivot->badge->tier }}
                        </span>
                        <span class="text-[9px] font-mono font-bold text-amber-300 bg-amber-950/40 px-1 py-0.5 rounded border border-amber-800/40">
                            +{{ $badgePivot->badge->points }}
                        </span>
                    </div>

                    <!-- Awarded Date -->
                    @if($badgePivot->awarded_at)
                        <span class="text-[10px] text-slate-500 font-mono mt-2" title="Awarded {{ $badgePivot->awarded_at->format('M j, Y') }}">
                            {{ $badgePivot->awarded_at->format('M j, Y') }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
