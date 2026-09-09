@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Lakes & Waters Hero Banner (Matching /profile standard) -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                <x-lucide-waves class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>Lakes & Waterbody Directory</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">GPS coordinates, bathymetric depths, fish species distribution, and waterbody regulations</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ url('/map/explorer') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5 cursor-pointer">
                <x-lucide-compass class="w-4 h-4 text-teal-400" />
                <span>Map Explorer</span>
            </a>

            <a href="{{ url('/lake/create') }}" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                <x-lucide-plus class="w-4 h-4" />
                <span>Add Waterbody</span>
            </a>
        </div>
    </div>

    <!-- Lakes Generic Livewire Data Table Card (Soft White Card matching /profile standard) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
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
    </div>
</div>
@endsection
