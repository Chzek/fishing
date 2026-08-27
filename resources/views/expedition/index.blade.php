@extends('layouts.app')

@section('content')
<div class="space-y-6">
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
