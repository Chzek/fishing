@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Lakes & Waters Hero Banner -->
    <x-pageHero
        title="Lakes & Waterbody Directory"
        subtitle="GPS coordinates, bathymetric depths, fish species distribution, and waterbody regulations"
        icon="lucide-waves"
    >
        <x-slot:actions>
            <a href="{{ url('/map/explorer') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer">
                <x-lucide-compass class="w-4 h-4 text-teal-400" />
                <span>Map Explorer</span>
            </a>

            <a href="{{ url('/lake/create') }}" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-plus class="w-4 h-4" />
                <span>Add Waterbody</span>
            </a>
        </x-slot:actions>
    </x-pageHero>

    <!-- Lakes Generic Livewire Data Table Card -->
    <x-card>
        <x-pageNavigation name="lake" :showReturn="false" />

        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\Lake::class,
            'columns' => [
                ['key' => 'name', 'label' => 'Lake Name', 'type' => 'lake_name', 'urlPrefix' => 'lake', 'sortable' => true, 'searchable' => true],
                ['key' => 'records_count', 'label' => 'Total Catches', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'records_count'],
                ['key' => 'visits', 'label' => 'Total Visits', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'visits'],
                ['key' => 'anglers_count', 'label' => 'Anglers Fished', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'anglers_count'],
            ],
            'searchPlaceholder' => 'Search lakes by name...',
            'itemName' => 'lakes',
            'perPage' => 15,
            'defaultSortBy' => 'name',
            'defaultSortOrder' => 'asc',
        ])
    </x-card>
</div>
@endsection
