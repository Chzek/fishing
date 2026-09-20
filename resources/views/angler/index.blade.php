@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Anglers Hero Banner -->
    <x-pageHero
        title="Anglers & Crew Directory"
        subtitle="Registered anglers, logbook contributors, production metrics, and expedition crew rosters"
        icon="lucide-users"
    >
        <x-slot:actions>
            <a href="{{ url('/angler/create') }}" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-user-plus class="w-4 h-4" />
                <span>Register Angler</span>
            </a>
        </x-slot:actions>
    </x-pageHero>

    <!-- Anglers Generic Livewire Data Table Card -->
    <x-card>
        <!-- Sub-navigation Tab Switcher (Soft Pastel Style) -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <a href="{{ url('/angler') }}" class="px-3.5 py-1.5 text-xs font-bold rounded-xl bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 flex items-center gap-2 shadow-2xs">
                    <x-lucide-users class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                    <span>Anglers Directory</span>
                </a>
                <a href="{{ url('/angler/stats') }}" class="px-3.5 py-1.5 text-xs font-bold rounded-xl text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex items-center gap-2">
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
    </x-card>
</div>
@endsection
