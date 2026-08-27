@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Sub-navigation Tab Switcher -->
    <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
        <div class="flex items-center gap-2">
            <a href="{{ url('/angler') }}" class="px-4 py-2 text-xs font-bold rounded-xl bg-teal-500/10 text-teal-700 border border-teal-500/20 flex items-center gap-2 shadow-2xs">
                <i data-lucide="users" class="w-4 h-4 text-teal-600"></i>
                <span>Anglers Directory</span>
            </a>
            <a href="{{ url('/angler/stats') }}" class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-slate-400"></i>
                <span>Angler Stats & Summary</span>
            </a>
        </div>

        <a href="{{ url('/angler/create') }}" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
            <span>Add Angler</span>
        </a>
    </div>

    <!-- Anglers Generic Livewire Data Table -->
    @livewire('components.generic-data-table', [
        'modelClass' => \Fishinglog\Models\Angler::class,
        'columns' => [
            ['key' => 'full_name', 'label' => 'Angler Name', 'type' => 'link', 'urlPrefix' => 'angler', 'urlParam' => 'id', 'sortable' => true, 'sortKey' => 'lastName', 'searchable' => true],
            ['key' => 'records_count', 'label' => 'Total Catches', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'records_count'],
            ['key' => 'lakes_count', 'label' => 'Lakes Visited', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'lakes_count'],
        ],
        'searchPlaceholder' => 'Search anglers by first or last name...',
        'itemName' => 'anglers',
        'perPage' => 10,
        'defaultSortBy' => 'lastName',
        'defaultSortOrder' => 'asc',
    ])
</div>
@endsection
