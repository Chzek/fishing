@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Results Counter Text -->
        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
            Showing <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $paginator->firstItem() }}</span> to <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $paginator->lastItem() }}</span> of <span class="font-bold text-slate-900 dark:text-slate-100 font-mono">{{ number_format($paginator->total()) }}</span> results
        </div>

        <div class="flex items-center gap-1.5 flex-wrap justify-center sm:justify-end">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-300 dark:text-slate-600 bg-slate-100/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 rounded-lg cursor-not-allowed select-none">
                    ← Prev
                </span>
            @else
                <button type="button" wire:click="previousPage" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 rounded-lg transition-all shadow-2xs hover:border-teal-500/50 cursor-pointer">
                    ← Prev
                </button>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-2.5 py-1 text-xs font-semibold text-slate-400 dark:text-slate-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/80 border border-teal-200/80 dark:border-teal-700 rounded-lg shadow-2xs font-mono">
                                    {{ $page }}
                                </span>
                            @else
                                <button type="button" wire:click="gotoPage({{ $page }})" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-teal-700 dark:hover:text-teal-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 rounded-lg transition-all hover:border-teal-500/40 shadow-2xs font-mono cursor-pointer">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 rounded-lg transition-all shadow-2xs hover:border-teal-500/50 cursor-pointer">
                    Next →
                </button>
            @else
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-300 dark:text-slate-600 bg-slate-100/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 rounded-lg cursor-not-allowed select-none">
                    Next →
                </span>
            @endif
        </div>
    </nav>
@endif
