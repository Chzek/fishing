@props([
    'class' => 'absolute inset-0 w-full h-full pointer-events-none select-none overflow-hidden',
])

<div {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
    <svg viewBox="0 0 400 180" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full" preserveAspectRatio="xMidYMid slice">
        <g transform="translate(260, 90) rotate(-40)">
            <!-- Ruler Background Ribbon (Tailwind Dark & Light Responsive) -->
            <rect x="-350" y="-24" width="700" height="48" class="fill-white/60 dark:fill-amber-400/[0.08] stroke-slate-400/40 dark:stroke-amber-400/30" stroke-width="2" />
            
            <!-- Bottom edge interior line -->
            <line x1="-350" y1="20" x2="350" y2="20" class="stroke-slate-400/25 dark:stroke-amber-400/20" stroke-width="1" />

            <!-- === Inch 26 === -->
            <line x1="-180" y1="-24" x2="-180" y2="-6" class="stroke-slate-500/50 dark:stroke-amber-300/70" stroke-width="2" stroke-linecap="round" />
            <text x="-180" y="14" font-size="12" font-weight="900" font-family="ui-monospace, monospace, sans-serif" class="fill-slate-600/50 dark:fill-amber-300/80" text-anchor="middle">26</text>
            <line x1="-168.75" y1="-24" x2="-168.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="-157.5" y1="-24" x2="-157.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="-146.25" y1="-24" x2="-146.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="-135" y1="-24" x2="-135" y2="-9" class="stroke-slate-500/45 dark:stroke-amber-400/60" stroke-width="1.5" />
            <line x1="-123.75" y1="-24" x2="-123.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="-112.5" y1="-24" x2="-112.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="-101.25" y1="-24" x2="-101.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />

            <!-- === Inch 27 === -->
            <line x1="-90" y1="-24" x2="-90" y2="-6" class="stroke-slate-500/50 dark:stroke-amber-300/70" stroke-width="2" stroke-linecap="round" />
            <text x="-90" y="14" font-size="12" font-weight="900" font-family="ui-monospace, monospace, sans-serif" class="fill-slate-600/50 dark:fill-amber-300/80" text-anchor="middle">27</text>
            <line x1="-78.75" y1="-24" x2="-78.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="-67.5" y1="-24" x2="-67.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="-56.25" y1="-24" x2="-56.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="-45" y1="-24" x2="-45" y2="-9" class="stroke-slate-500/45 dark:stroke-amber-400/60" stroke-width="1.5" />
            <line x1="-33.75" y1="-24" x2="-33.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="-22.5" y1="-24" x2="-22.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="-11.25" y1="-24" x2="-11.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />

            <!-- === Inch 28 === -->
            <line x1="0" y1="-24" x2="0" y2="-6" class="stroke-slate-500/50 dark:stroke-amber-300/70" stroke-width="2" stroke-linecap="round" />
            <text x="0" y="14" font-size="12" font-weight="900" font-family="ui-monospace, monospace, sans-serif" class="fill-slate-600/50 dark:fill-amber-300/80" text-anchor="middle">28</text>
            <line x1="11.25" y1="-24" x2="11.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="22.5" y1="-24" x2="22.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="33.75" y1="-24" x2="33.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="45" y1="-24" x2="45" y2="-9" class="stroke-slate-500/45 dark:stroke-amber-400/60" stroke-width="1.5" />
            <line x1="56.25" y1="-24" x2="56.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="67.5" y1="-24" x2="67.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="78.75" y1="-24" x2="78.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />

            <!-- === Inch 29 === -->
            <line x1="90" y1="-24" x2="90" y2="-6" class="stroke-slate-500/50 dark:stroke-amber-300/70" stroke-width="2" stroke-linecap="round" />
            <text x="90" y="14" font-size="12" font-weight="900" font-family="ui-monospace, monospace, sans-serif" class="fill-slate-600/50 dark:fill-amber-300/80" text-anchor="middle">29</text>
            <line x1="101.25" y1="-24" x2="101.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="112.5" y1="-24" x2="112.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="123.75" y1="-24" x2="123.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="135" y1="-24" x2="135" y2="-9" class="stroke-slate-500/45 dark:stroke-amber-400/60" stroke-width="1.5" />
            <line x1="146.25" y1="-24" x2="146.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="157.5" y1="-24" x2="157.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="168.75" y1="-24" x2="168.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />

            <!-- === Inch 30 === -->
            <line x1="180" y1="-24" x2="180" y2="-6" class="stroke-slate-500/50 dark:stroke-amber-300/70" stroke-width="2" stroke-linecap="round" />
            <text x="180" y="14" font-size="12" font-weight="900" font-family="ui-monospace, monospace, sans-serif" class="fill-slate-600/50 dark:fill-amber-300/80" text-anchor="middle">30</text>
            <line x1="191.25" y1="-24" x2="191.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="202.5" y1="-24" x2="202.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="213.75" y1="-24" x2="213.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="225" y1="-24" x2="225" y2="-9" class="stroke-slate-500/45 dark:stroke-amber-400/60" stroke-width="1.5" />
            <line x1="236.25" y1="-24" x2="236.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="247.5" y1="-24" x2="247.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="258.75" y1="-24" x2="258.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />

            <!-- === Inch 31 === -->
            <line x1="270" y1="-24" x2="270" y2="-6" class="stroke-slate-500/50 dark:stroke-amber-300/70" stroke-width="2" stroke-linecap="round" />
            <text x="270" y="14" font-size="12" font-weight="900" font-family="ui-monospace, monospace, sans-serif" class="fill-slate-600/50 dark:fill-amber-300/80" text-anchor="middle">31</text>
            <line x1="281.25" y1="-24" x2="281.25" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="292.5" y1="-24" x2="292.5" y2="-13" class="stroke-slate-500/35 dark:stroke-amber-400/45" stroke-width="1.25" />
            <line x1="303.75" y1="-24" x2="303.75" y2="-17" class="stroke-slate-500/25 dark:stroke-amber-400/30" stroke-width="1" />
            <line x1="315" y1="-24" x2="315" y2="-9" class="stroke-slate-500/45 dark:stroke-amber-400/60" stroke-width="1.5" />

            <!-- === Inch 32 === -->
            <line x1="360" y1="-24" x2="360" y2="-6" class="stroke-slate-500/50 dark:stroke-amber-300/70" stroke-width="2" stroke-linecap="round" />
            <text x="360" y="14" font-size="12" font-weight="900" font-family="ui-monospace, monospace, sans-serif" class="fill-slate-600/50 dark:fill-amber-300/80" text-anchor="middle">32</text>
        </g>
    </svg>
</div>
