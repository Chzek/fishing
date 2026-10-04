@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <x-statusAlert />

    <!-- Top Navigation Breadcrumb & Back Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
            <a href="{{ route('journal.index') }}" class="hover:text-amber-500 transition-colors flex items-center gap-1">
                <x-lucide-book-open class="w-4 h-4" />
                <span>Journals</span>
            </a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 dark:text-slate-200 font-bold truncate max-w-xs">{{ $entry->title }}</span>
        </div>

        <div class="flex items-center gap-2">
            @if($entry->expedition)
                <a href="{{ url('/expedition/' . $entry->expedition->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition-colors">
                    <x-lucide-ship class="w-3.5 h-3.5 text-teal-400" />
                    <span>View Expedition Dossier</span>
                </a>
            @endif
            <a href="{{ route('journal.index') }}" class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700 transition-colors">
                <x-lucide-arrow-left class="w-3.5 h-3.5" />
                <span>All Journals</span>
            </a>
        </div>
    </div>

    <!-- Main Split-Screen Reader Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Side (5 Cols): Scanned Handwritten Pages Gallery & Zoom -->
        <div class="lg:col-span-5 space-y-4">
            <x-card title="Handwritten Original Scan" icon="file-text" iconColor="amber" badge="{{ $entry->pages->count() }} Page{{ $entry->pages->count() > 1 ? 's' : '' }}" badgeVariant="amber">
                @if($entry->pages->count() > 0)
                    <div x-data="{ selectedPage: '{{ $entry->pages->first()->url }}' }" class="space-y-3">
                        <!-- Main Selected Photo Zoom Stage -->
                        <div class="relative aspect-3/4 rounded-xl overflow-hidden bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-md group">
                            <img :src="selectedPage" alt="Original Journal Page" class="w-full h-full object-contain cursor-zoom-in" @click="openPhotoLightbox(selectedPage, '{{ addslashes($entry->title) }}')">
                            
                            <div class="absolute bottom-2 right-2 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-mono font-bold px-2 py-1 rounded-md border border-slate-700 flex items-center gap-1 pointer-events-none">
                                <x-lucide-zoom-in class="w-3 h-3 text-amber-400" />
                                <span>Click to Expand</span>
                            </div>
                        </div>

                        <!-- Multi-page thumbnail strip if more than 1 page -->
                        @if($entry->pages->count() > 1)
                            <div class="grid grid-cols-4 gap-2 pt-1">
                                @foreach($entry->pages as $page)
                                    <button 
                                        type="button" 
                                        @click="selectedPage = '{{ $page->url }}'"
                                        class="aspect-3/4 rounded-lg overflow-hidden border-2 transition-all cursor-pointer relative bg-slate-950"
                                        :class="selectedPage === '{{ $page->url }}' ? 'border-amber-500 shadow-sm ring-2 ring-amber-500/20' : 'border-slate-200 dark:border-slate-700 opacity-70 hover:opacity-100'"
                                    >
                                        <img src="{{ $page->url }}" alt="Page thumbnail" class="w-full h-full object-cover">
                                        <span class="absolute bottom-1 right-1 bg-slate-900/80 text-white font-mono text-[9px] px-1 rounded">
                                            #{{ $page->sequence_order }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-10 text-slate-400 text-xs italic">
                        No high-res photo scan linked to this entry.
                    </div>
                @endif
            </x-card>

            <!-- Tagged Lakes & Crew Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tagged Crew -->
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Logged Crew</span>
                    @if($entry->anglers->count() > 0)
                        <div class="space-y-1.5">
                            @foreach($entry->anglers as $angler)
                                <a href="/angler/{{ $angler->id }}" class="flex items-center gap-2 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                                    <x-anglerAvatar :angler="$angler" size="xs" />
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $angler->full_name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-xs text-slate-400 italic">No specific anglers tagged.</div>
                    @endif
                </div>

                <!-- Tagged Lakes -->
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Mentioned Waters</span>
                    @if($entry->lakes->count() > 0)
                        <div class="space-y-1.5">
                            @foreach($entry->lakes as $lake)
                                <a href="/lake/{{ $lake->id }}" class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-400 transition-colors">
                                    <x-lucide-map-pin class="w-3.5 h-3.5 text-teal-500 shrink-0" />
                                    <span class="truncate">{{ $lake->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-xs text-slate-400 italic">No specific lakes tagged.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Side (7 Cols): Formatted Transcript & Story -->
        <div class="lg:col-span-7 space-y-5">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5">
                <!-- Header -->
                <div class="space-y-2 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <div class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                            <x-lucide-calendar class="w-4 h-4" />
                            <span>{{ $entry->entry_date ? $entry->entry_date->format('l, F j, Y') : 'Historical Date' }}</span>
                        </div>
                        @if($entry->weather_summary)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80 font-mono">
                                <x-lucide-cloud-sun class="w-3.5 h-3.5 text-amber-500" />
                                <span>{{ $entry->weather_summary }}</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ $entry->title }}
                    </h1>
                </div>

                <!-- Highlights Quote Banner -->
                @if($entry->highlights)
                    <div class="p-4 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/80 space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-400 flex items-center gap-1">
                            <x-lucide-sparkles class="w-3.5 h-3.5" />
                            <span>Standout Highlights & Anecdotes</span>
                        </span>
                        <p class="text-xs font-medium text-slate-800 dark:text-slate-200 italic leading-relaxed">
                            "{{ $entry->highlights }}"
                        </p>
                    </div>
                @endif

                <!-- Markdown Transcription Content -->
                <div class="prose dark:prose-invert max-w-none text-slate-800 dark:text-slate-200 text-sm leading-relaxed space-y-4">
                    {!! \Illuminate\Support\Str::markdown($entry->body_markdown) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
