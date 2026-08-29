@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $lake->name }} Visits Log</h1>
                    <p class="text-xs text-slate-500">Historical trip visit records</p>
                </div>
            </div>
            <a href="/lake/{{ $lake->id }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Back to Lake</a>
        </div>

        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\Record::class,
            'lakeId' => (string) $lake->id,
            'with' => ['angler', 'lake', 'fishBreed', 'lure'],
            'columns' => [
                ['key' => 'caught', 'label' => 'Visit Date', 'type' => 'date', 'sortable' => true],
                ['key' => 'angler.lastName', 'label' => 'Angler', 'type' => 'angler_name', 'sortable' => true],
                ['key' => 'fishBreed.name', 'label' => 'Species', 'type' => 'species_name', 'sortable' => true],
                ['key' => 'length', 'label' => 'Length / Weight', 'type' => 'catch_length_weight', 'sortable' => true],
            ],
            'searchPlaceholder' => 'Search lake visit catches...',
            'itemName' => 'catches',
            'perPage' => 15,
        ])   </div>
    </div>
</div>
@endsection
