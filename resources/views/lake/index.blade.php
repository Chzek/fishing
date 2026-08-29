@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="lake" :showReturn="false" />

        <!-- Lakes Generic Livewire Data Table -->
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
