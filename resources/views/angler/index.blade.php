@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Anglers Hero Banner (Matching /profile standard) -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                <x-lucide-users class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>Anglers & Crew Directory</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Registered anglers, logbook contributors, production metrics, and expedition crew rosters</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ url('/angler/create') }}" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-user-plus class="w-4 h-4" />
                <span>Register Angler</span>
            </a>
        </div>
    </div>

    <!-- Anglers Generic Livewire Data Table Card (Soft White Card matching /profile standard) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <!-- Sub-navigation Tab Switcher (Soft Pastel Style) -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <a href="{{ url('/angler') }}" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center gap-2 shadow-2xs">
                    <x-lucide-users class="w-4 h-4 text-teal-600" />
                    <span>Anglers Directory</span>
                </a>
                <a href="{{ url('/angler/stats') }}" class="px-3.5 py-1.5 text-xs font-bold rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors flex items-center gap-2">
                    <x-lucide-bar-chart-3 class="w-4 h-4 text-slate-400" />
                    <span>Angler Stats & Summary</span>
                </a>
            </div>
        </div>

        <!-- Anglers Generic Livewire Data Table -->
        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\Angler::class,
            'columns' => [
                ['key' => 'lastName', 'label' => 'Angler Name', 'type' => 'angler_name', 'sortable' => true, 'sortKey' => 'lastName', 'searchable' => true],
                ['key' => 'records_count', 'label' => 'Total Catches', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'records_count'],
                ['key' => 'lakes_count', 'label' => 'Lakes Visited', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'lakes_count'],
            ],
            'searchPlaceholder' => 'Search anglers by first or last name...',
            'itemName' => 'anglers',
            'perPage' => 10,
            'defaultSortBy' => 'records_count',
            'defaultSortOrder' => 'desc',
        ])
    </div>
</div>
@endsection
