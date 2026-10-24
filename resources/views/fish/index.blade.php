@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Hero Banner -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                <x-lucide-book-open class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                    <span>Fish Species & Taxonomy Guide</span>
                    <span class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">{{ $totalBreedsCount }} Species</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium pt-0.5">Biological profiles, taxonomic classification, and catch telemetry across fresh & saltwater species</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="/record/quick" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer">
                <x-lucide-zap class="w-4 h-4 text-teal-400" />
                <span>Log Catch</span>
            </a>
            <a href="/fish/breed/create" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-plus class="w-4 h-4" />
                <span>Add Species</span>
            </a>
            <a href="/fish/family/create" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <x-lucide-folder-plus class="w-3.5 h-3.5 text-teal-400" />
                <span>Add Family</span>
            </a>
        </div>
    </div>

    <!-- Taxonomy Overview Telemetry Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-100 dark:border-teal-800/60 flex items-center justify-center shrink-0">
                <x-lucide-fish class="w-5 h-5" />
            </div>
            <div>
                <span class="text-2xl font-black text-slate-900 dark:text-white block leading-tight font-mono">{{ $totalBreedsCount }}</span>
                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tracked Species</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800/60 flex items-center justify-center shrink-0">
                <x-lucide-layers class="w-5 h-5" />
            </div>
            <div>
                <span class="text-2xl font-black text-slate-900 dark:text-white block leading-tight font-mono">{{ $totalFamiliesCount }}</span>
                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Taxonomic Families</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800/60 flex items-center justify-center shrink-0">
                <x-lucide-award class="w-5 h-5" />
            </div>
            <div>
                <span class="text-2xl font-black text-slate-900 dark:text-white block leading-tight font-mono">{{ $totalCatchesCount }}</span>
                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Catches Logged</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 shadow-sm border border-slate-200/80 dark:border-slate-800 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/60 flex items-center justify-center shrink-0">
                <x-lucide-flame class="w-5 h-5" />
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-sm font-bold text-slate-900 dark:text-white block truncate">{{ $topSpecies?->name ?? 'None' }}</span>
                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                    Most Caught ({{ $topSpecies?->records_count ?? 0 }})
                </span>
            </div>
        </div>
    </div>

    <!-- Reactive Fish Species & Taxonomy Directory -->
    @livewire('directory.fish-directory', [
        'initialFamily' => $selectedFamilyId ?? request('family'),
        'initialSearch' => $search ?? request('search'),
    ])
</div>
@endsection
