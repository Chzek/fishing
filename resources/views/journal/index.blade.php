@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <x-statusAlert />

    <!-- Page Hero Header -->
    <x-pageHero 
        title="Expedition Journals & Cabin Logs" 
        subtitle="Digitized handwritten Canadian logbooks, camp journals, and historical trip telemetry" 
        icon="lucide-book-open" 
        badge="{{ $totalEntriesCount }} Log Entries • {{ $totalPagesCount }} Scanned Pages" 
        badgeVariant="amber"
    >
        <x-slot:actions>
            <a href="{{ url('/expedition') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold py-2.5 px-4 rounded-xl border border-slate-700 transition-colors">
                <x-lucide-ship class="w-4 h-4 text-teal-400" />
                <span>Expeditions Directory</span>
            </a>
        </x-slot:actions>
    </x-pageHero>

    <!-- Search & Filter Controls -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('journal.index') }}" method="GET" class="w-full md:max-w-md relative">
            @if($selectedYear)
                <input type="hidden" name="year" value="{{ $selectedYear }}">
            @endif
            <x-lucide-search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5 pointer-events-none" />
            <input 
                type="text" 
                name="search" 
                value="{{ $search }}" 
                placeholder="Search transcripts, rapids, quotes, lakes, or anglers..." 
                class="w-full h-10 pl-9 pr-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-colors"
            >
        </form>

        <!-- Year Filter Badges -->
        <div class="flex items-center gap-1.5 overflow-x-auto max-w-full pb-1 sm:pb-0">
            <a href="{{ route('journal.index', array_filter(['search' => $search])) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ empty($selectedYear) ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                All Years
            </a>
            @foreach($availableYears as $yr)
                <a href="{{ route('journal.index', array_filter(['year' => $yr, 'search' => $search])) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold font-mono transition-colors {{ $selectedYear == $yr ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    {{ $yr }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Journal Entries Grid -->
    @if($entries->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($entries as $entry)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-amber-400 dark:hover:border-amber-600 transition-all flex flex-col justify-between overflow-hidden group">
                    <!-- Top Image Banner Thumbnail if page exists -->
                    @php
                        $firstPage = $entry->pages->first();
                    @endphp

                    @if($firstPage)
                        <a href="{{ route('journal.show', $entry->id) }}" class="relative aspect-16/9 bg-slate-950 overflow-hidden block">
                            <img src="{{ $firstPage->url }}" alt="{{ $entry->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 opacity-90 group-hover:opacity-100">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                            <div class="absolute top-2.5 right-2.5 flex items-center gap-1.5">
                                <span class="bg-slate-900/90 text-amber-400 border border-amber-400/30 text-[10px] font-mono font-bold px-2 py-0.5 rounded-md backdrop-blur-xs flex items-center gap-1">
                                    <x-lucide-file-text class="w-3 h-3" />
                                    <span>{{ $entry->pages->count() }} Page{{ $entry->pages->count() > 1 ? 's' : '' }}</span>
                                </span>
                            </div>
                            <div class="absolute bottom-2.5 left-3 right-3 text-white">
                                <div class="text-[11px] font-mono font-bold text-amber-300 flex items-center gap-1">
                                    <x-lucide-calendar class="w-3 h-3" />
                                    <span>{{ $entry->entry_date ? $entry->entry_date->format('M j, Y') : 'Historical Entry' }}</span>
                                </div>
                            </div>
                        </a>
                    @endif

                    <div class="p-5 space-y-3 flex-1 flex flex-col justify-between">
                        <div class="space-y-2">
                            @if(!$firstPage)
                                <div class="text-[11px] font-mono font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                    <x-lucide-calendar class="w-3.5 h-3.5" />
                                    <span>{{ $entry->entry_date ? $entry->entry_date->format('M j, Y') : 'Historical Entry' }}</span>
                                </div>
                            @endif

                            <h3 class="font-bold text-slate-900 dark:text-white text-base tracking-tight group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                                <a href="{{ route('journal.show', $entry->id) }}">{{ $entry->title }}</a>
                            </h3>

                            @if($entry->weather_summary)
                                <div class="text-xs text-slate-600 dark:text-slate-400 flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/80 px-2.5 py-1.5 rounded-lg border border-slate-200/50 dark:border-slate-700/50">
                                    <x-lucide-cloud-sun class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                                    <span class="truncate">{{ $entry->weather_summary }}</span>
                                </div>
                            @endif

                            @if($entry->highlights)
                                <blockquote class="text-xs text-slate-700 dark:text-slate-300 italic border-l-2 border-amber-500 pl-2.5 line-clamp-3">
                                    "{{ $entry->highlights }}"
                                </blockquote>
                            @endif
                        </div>

                        <!-- Card Footer with Tagged Entities -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                            @if($entry->expedition)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 text-[11px]">Expedition:</span>
                                    <a href="{{ url('/expedition/' . $entry->expedition->id) }}" class="font-bold text-teal-600 dark:text-teal-400 hover:underline truncate max-w-[180px]">
                                        {{ $entry->expedition->description }}
                                    </a>
                                </div>
                            @endif

                            @if($entry->anglers->count() > 0)
                                <div class="flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400 truncate">
                                    <x-lucide-users class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                    <span class="truncate">{{ $entry->anglers->pluck('full_name')->implode(', ') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $entries->links() }}
        </div>
    @else
        <x-emptyState 
            icon="book-open" 
            title="No Journal Entries Found" 
            description="No logbook entries match your filter criteria." 
        />
    @endif
</div>
@endsection
