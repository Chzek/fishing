@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Expeditions Hero Banner -->
    <x-pageHero
        title="Expeditions & Multi-Day Outings"
        subtitle="Multi-angler wilderness adventures, crew logs, daily journal posts, and trip catches"
        icon="lucide-ship"
    >
        <x-slot:actions>
            <a href="{{ url('/record/quick') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer">
                <x-lucide-zap class="w-4 h-4 text-teal-400" />
                <span>Log Catch</span>
            </a>

            <a href="{{ url('/expedition/create') }}" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-plus class="w-4 h-4" />
                <span>Plan Expedition</span>
            </a>
        </x-slot:actions>
    </x-pageHero>

    <!-- Expeditions Generic Livewire Data Table Card -->
    <x-card>
        <x-pageNavigation name="expedition" />

        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
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
    </x-card>
</div>
@endsection
