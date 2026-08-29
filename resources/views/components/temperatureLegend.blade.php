@props([
    'size' => 'sm',
    'showBreakdown' => false,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-2 select-none']) }}>
    <!-- Temperature Scale Gradient Bar -->
    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-950/80 border border-slate-800 rounded-xl shadow-inner w-full justify-between">
        <span class="font-mono text-[11px] font-bold text-blue-400">40°F</span>
        
        <div class="relative flex items-center flex-1 mx-2">
            <div class="w-full h-2 rounded-full bg-gradient-to-r from-blue-400 via-cyan-400 via-teal-400 via-emerald-400 via-amber-400 via-orange-400 to-rose-500 shadow-2xs border border-white/20"></div>
            <span class="absolute left-1/2 -translate-x-1/2 -top-1 font-mono text-[9px] font-extrabold text-amber-300 bg-slate-900 px-1 py-0.2 rounded border border-amber-400/40 shadow-2xs" title="70°F Scale Center">70°F</span>
        </div>

        <span class="font-mono text-[11px] font-bold text-rose-400">90°F+</span>
    </div>

    @if($showBreakdown)
        <!-- 4-Interval Color Breakdown Grid -->
        <div class="grid grid-cols-2 gap-1.5 w-full text-[10px] font-medium pt-0.5">
            <div class="flex items-center gap-1.5 px-2 py-1 bg-blue-950/60 border border-blue-400/40 rounded-lg text-blue-200">
                <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                <span>&lt; 40°F Ice Cold</span>
            </div>
            <div class="flex items-center gap-1.5 px-2 py-1 bg-teal-950/60 border border-teal-400/40 rounded-lg text-teal-200">
                <span class="w-2 h-2 rounded-full bg-teal-400 shrink-0"></span>
                <span>40–59°F Cool</span>
            </div>
            <div class="flex items-center gap-1.5 px-2 py-1 bg-amber-950/60 border border-amber-400/40 rounded-lg text-amber-200">
                <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                <span>60–79°F Warm</span>
            </div>
            <div class="flex items-center gap-1.5 px-2 py-1 bg-rose-950/60 border border-rose-400/40 rounded-lg text-rose-200">
                <span class="w-2 h-2 rounded-full bg-rose-400 shrink-0"></span>
                <span>≥ 80°F Hot</span>
            </div>
        </div>
    @endif
</div>
