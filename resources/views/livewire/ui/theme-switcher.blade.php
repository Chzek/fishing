<div 
    x-data="{
        activeTheme: @entangle('theme'),
        applyTheme(theme) {
            this.activeTheme = theme;
            localStorage.setItem('theme', theme);
            const isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            $wire.setTheme(theme);
        }
    }"
    x-on:theme-changed.window="
        if ($event.detail && $event.detail.theme) {
            activeTheme = $event.detail.theme;
            localStorage.setItem('theme', activeTheme);
            const isDark = activeTheme === 'dark' || (activeTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    "
    class="inline-flex p-1 bg-slate-950/80 dark:bg-slate-900 border border-slate-800 rounded-xl shadow-inner {{ $compact ? 'w-full justify-between' : 'w-full' }}"
>
    <!-- System Option -->
    <button 
        type="button" 
        @click="applyTheme('system')"
        :class="activeTheme === 'system' ? 'bg-teal-500/20 text-teal-300 font-bold border border-teal-500/40 shadow-xs' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border border-transparent'"
        class="flex-1 flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs transition-all cursor-pointer"
        title="System Auto Preference"
    >
        <x-lucide-monitor class="w-3.5 h-3.5 shrink-0" />
        @if(!$compact)
            <span class="truncate">System</span>
        @endif
    </button>

    <!-- Light Option -->
    <button 
        type="button" 
        @click="applyTheme('light')"
        :class="activeTheme === 'light' ? 'bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40 shadow-xs' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border border-transparent'"
        class="flex-1 flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs transition-all cursor-pointer"
        title="Light / Sunlight Outdoor Mode"
    >
        <x-lucide-sun class="w-3.5 h-3.5 shrink-0" />
        @if(!$compact)
            <span class="truncate">Light</span>
        @endif
    </button>

    <!-- Dark Option -->
    <button 
        type="button" 
        @click="applyTheme('dark')"
        :class="activeTheme === 'dark' ? 'bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/40 shadow-xs' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 border border-transparent'"
        class="flex-1 flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg text-xs transition-all cursor-pointer"
        title="Dark / Night Cockpit Mode"
    >
        <x-lucide-moon class="w-3.5 h-3.5 shrink-0" />
        @if(!$compact)
            <span class="truncate">Dark</span>
        @endif
    </button>
</div>
