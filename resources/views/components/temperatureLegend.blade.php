@props([
    'showLabels' => true,
])

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-3 py-1 bg-slate-800/90 border border-slate-700/80 rounded-full text-xs font-semibold text-slate-300 shadow-inner select-none']) }} title="Catch Temperature Scale: <40°F (Blue) • 50°F (Teal) • 70°F (Amber) • 90°F+ (Rose)">
    <span class="text-[10px] font-mono text-blue-400 font-bold shrink-0">40°F</span>
    
    <!-- Smooth Multi-Stop Temperature Gradient Pill Bar -->
    <div class="relative flex items-center">
        <div class="w-20 sm:w-28 h-2 rounded-full bg-gradient-to-r from-blue-400 via-cyan-400 via-teal-400 via-emerald-400 via-amber-400 via-orange-400 to-rose-500 shadow-2xs border border-white/20"></div>
        <span class="absolute left-1/2 -translate-x-1/2 -top-1 font-mono text-[9px] font-extrabold text-amber-300 bg-slate-900 px-1 py-0.2 rounded-md border border-amber-400/40 shadow-xs" title="70°F Warm Scale Center">70°F</span>
    </div>

    <span class="text-[10px] font-mono text-rose-400 font-bold shrink-0">90°F</span>
</div>
