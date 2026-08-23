@props(['name', 'showReturn' => false])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
        <i data-lucide="layers" class="w-5 h-5 text-teal-600"></i>
        <span>{{ ucfirst($name) }} Index</span>
    </h1>
    <div class="flex items-center gap-2">
        @if(view()->exists(implode(".", [$name, 'create'])))
            <a href="/{{ $name }}/create" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Add {{ ucfirst($name) }}</span>
            </a>
        @endif
        @if($showReturn)
            <a href="/" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Return</span>
            </a>
        @endif
    </div>
</div>