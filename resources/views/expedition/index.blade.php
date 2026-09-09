@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Expeditions Hero Banner (Matching /profile standard) -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                <x-lucide-ship class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>Expeditions & Multi-Day Outings</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Multi-angler wilderness adventures, crew logs, daily journal posts, and trip catches</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ url('/record/quick') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer">
                <x-lucide-zap class="w-4 h-4 text-teal-400" />
                <span>Log Catch</span>
            </a>

            <a href="{{ url('/expedition/create') }}" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-plus class="w-4 h-4" />
                <span>Plan Expedition</span>
            </a>
        </div>
    </div>

    <!-- Expeditions Generic Livewire Data Table Card (Soft White Card matching /profile standard) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="expedition" />

        <p class="text-xs text-slate-500 leading-relaxed">
            An <strong>expedition</strong> is a group of like-minded anglers gathering to adventure into the wilderness in pursuit of trophy catches and multi-day fishing trips.
        </p>

        <!-- Expeditions Generic Livewire Data Table -->
        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\Expedition::class,
            'columns' => [
                ['key' => 'description', 'label' => 'Trip Description', 'type' => 'expedition_desc', 'sortable' => true, 'searchable' => true],
                ['key' => 'start', 'label' => 'Start Date', 'type' => 'date', 'sortable' => true],
                ['key' => 'finish', 'label' => 'Finish Date', 'type' => 'date', 'sortable' => true],
                ['key' => 'crews_count', 'label' => 'Crew Anglers', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'crews_count'],
                ['key' => 'records_count', 'label' => 'Catches Logged', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'records_count'],
                ['key' => 'posts_count', 'label' => 'Trip Posts', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'posts_count'],
            ],
            'searchPlaceholder' => 'Quick filter expeditions by description or date...',
            'itemName' => 'expeditions',
            'perPage' => 15,
            'defaultSortBy' => 'start',
            'defaultSortOrder' => 'desc',
        ])
    </div>
</div>
@endsection
