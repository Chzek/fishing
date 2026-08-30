@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ tab: 'records' }">
    <!-- Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/30 text-rose-400 flex items-center justify-center shrink-0">
                <x-lucide-trash-2 class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Admin Trash Bin & Recovery Console</h1>
                <p class="text-xs text-slate-400">Restore soft-deleted items or permanently purge deleted data from storage</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                ← Admin Console
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-4 rounded-xl shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <!-- Category Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs font-bold">
        <button @click="tab = 'records'" :class="tab === 'records' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Catches ({{ count($trashedCatches) }})
        </button>
        <button @click="tab = 'lakes'" :class="tab === 'lakes' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Lakes ({{ count($trashedLakes) }})
        </button>
        <button @click="tab = 'anglers'" :class="tab === 'anglers' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Anglers ({{ count($trashedAnglers) }})
        </button>
        <button @click="tab = 'lures'" :class="tab === 'lures' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Lures ({{ count($trashedLures) }})
        </button>
        <button @click="tab = 'expeditions'" :class="tab === 'expeditions' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-3.5 py-2 rounded-xl border transition-colors cursor-pointer">
            Expeditions ({{ count($trashedExpeditions) }})
        </button>
    </div>

    <!-- Catches Tab -->
    <div x-show="tab === 'records'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Catches</h2>
        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\Record::class,
            'onlyTrashed' => true,
            'with' => ['angler', 'lake', 'fishBreed'],
            'columns' => [
                ['key' => 'caught', 'label' => 'Catch Date', 'type' => 'date', 'sortable' => true],
                ['key' => 'angler.lastName', 'label' => 'Angler', 'type' => 'angler_name', 'sortable' => true],
                ['key' => 'lake.name', 'label' => 'Lake', 'type' => 'lake_link', 'sortable' => true],
                ['key' => 'length', 'label' => 'Length / Weight', 'type' => 'catch_length_weight', 'sortable' => true],
                ['key' => 'deleted_at', 'label' => 'Deleted At', 'type' => 'date', 'sortable' => true],
            ],
            'searchPlaceholder' => 'Search deleted catches...',
            'itemName' => 'catches',
            'perPage' => 10,
        ])
    </div>

    <!-- Lakes Tab -->
    <div x-show="tab === 'lakes'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Lakes & Waterbodies</h2>
        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\Lake::class,
            'onlyTrashed' => true,
            'columns' => [
                ['key' => 'name', 'label' => 'Lake Name', 'type' => 'lake_name', 'sortable' => true, 'searchable' => true],
                ['key' => 'deleted_at', 'label' => 'Deleted At', 'type' => 'date', 'sortable' => true],
            ],
            'searchPlaceholder' => 'Search deleted lakes...',
            'itemName' => 'lakes',
            'perPage' => 10,
        ])
    </div>

    <!-- Anglers Tab -->
    <div x-show="tab === 'anglers'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Angler Profiles</h2>
        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\Angler::class,
            'onlyTrashed' => true,
            'columns' => [
                ['key' => 'lastName', 'label' => 'Angler Name', 'type' => 'angler_name', 'sortable' => true, 'searchable' => true],
                ['key' => 'deleted_at', 'label' => 'Deleted At', 'type' => 'date', 'sortable' => true],
            ],
            'searchPlaceholder' => 'Search deleted anglers...',
            'itemName' => 'anglers',
            'perPage' => 10,
        ])
    </div>

    <!-- Lures Tab -->
    <div x-show="tab === 'lures'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Lures</h2>
        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\Lure::class,
            'onlyTrashed' => true,
            'columns' => [
                ['key' => 'name', 'label' => 'Lure Name', 'sortable' => true, 'searchable' => true],
                ['key' => 'deleted_at', 'label' => 'Deleted At', 'type' => 'date', 'sortable' => true],
            ],
            'searchPlaceholder' => 'Search deleted lures...',
            'itemName' => 'lures',
            'perPage' => 10,
        ])
    </div>

    <!-- Expeditions Tab -->
    <div x-show="tab === 'expeditions'" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <h2 class="font-bold text-slate-900 text-sm">Soft-Deleted Expeditions</h2>
        @livewire('components.generic-data-table', [
            'modelClass' => \Fishinglog\Models\Expedition::class,
            'onlyTrashed' => true,
            'columns' => [
                ['key' => 'description', 'label' => 'Description', 'type' => 'expedition_desc', 'sortable' => true, 'searchable' => true],
                ['key' => 'start', 'label' => 'Start Date', 'type' => 'date', 'sortable' => true],
                ['key' => 'finish', 'label' => 'End Date', 'type' => 'date', 'sortable' => true],
                ['key' => 'deleted_at', 'label' => 'Deleted At', 'type' => 'date', 'sortable' => true],
            ],
            'searchPlaceholder' => 'Search deleted expeditions...',
            'itemName' => 'expeditions',
            'perPage' => 10,
        ])
    </div>
</div>
@endsection
