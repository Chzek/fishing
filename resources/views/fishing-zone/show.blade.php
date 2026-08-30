@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Zone Hero Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-black px-3 py-1 rounded-full font-mono">
                    {{ $fishingZone->code }}
                </span>
                <h1 class="text-2xl font-extrabold text-white tracking-tight pt-1">{{ $fishingZone->name }}</h1>
                <p class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
                    <x-lucide-globe class="w-3.5 h-3.5 text-teal-400" />
                    <span>{{ $fishingZone->province_state }}, {{ $fishingZone->country }} License Management Zone</span>
                </p>
            </div>

            @if($fishingZone->regulations_url)
                <a href="{{ $fishingZone->regulations_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white text-xs font-bold rounded-xl shadow-lg shadow-teal-950/40 transition-all shrink-0">
                    <x-lucide-external-link class="w-4 h-4" />
                    <span>Official Ontario Regs Guide</span>
                </a>
            @endif
        </div>

        @if($fishingZone->description)
            <div class="pt-3 border-t border-slate-800 text-xs text-slate-300 leading-relaxed max-w-3xl">
                {{ $fishingZone->description }}
            </div>
        @endif
    </div>

    <!-- Species Regulations & Limits Card with Local Search & Multi-Sort -->
    @if(isset($fishingZone->rules) && count($fishingZone->rules) > 0)
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <x-lucide-book-open class="w-5 h-5 text-indigo-600" />
                    <span>Species Regulations & Possession Limits ({{ $fishingZone->code }})</span>
                </h2>
                <span class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200">
                    {{ count($fishingZone->rules) }} Species Guidelines
                </span>
            </div>

            @livewire('components.generic-data-table', [
                'modelClass' => \Fishinglog\Models\FishingRule::class,
                'fishingZoneId' => (string) $fishingZone->id,
                'columns' => [
                    ['key' => 'species_name', 'label' => 'Target Species', 'sortable' => true, 'searchable' => true],
                    ['key' => 'season', 'label' => 'Open Season', 'sortable' => true],
                    ['key' => 'sport_limit', 'label' => 'Sport Limit (S)', 'type' => 'count', 'align' => 'center', 'sortable' => true],
                    ['key' => 'conservation_limit', 'label' => 'Conservation Limit (C)', 'type' => 'count', 'align' => 'center', 'sortable' => true],
                    ['key' => 'size_restriction', 'label' => 'Size Restrictions', 'sortable' => true],
                ],
                'searchPlaceholder' => 'Quick filter species regulations...',
                'itemName' => 'regulations',
                'perPage' => 15,
            ])
        </div>
    @endif

    <!-- Assigned Lakes in Zone -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <x-lucide-waves class="w-5 h-5 text-teal-600" />
                <span>Registered Waterbodies in {{ $fishingZone->code }}</span>
            </h2>
            <span class="text-xs font-mono font-bold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-200">
                {{ count($fishingZone->lakes) }} Lakes
            </span>
        </div>

        @if(count($fishingZone->lakes) > 0)
            @livewire('components.generic-data-table', [
                'modelClass' => \Fishinglog\Models\Lake::class,
                'fishingZoneId' => (string) $fishingZone->id,
                'columns' => [
                    ['key' => 'name', 'label' => 'Lake Name', 'type' => 'lake_name', 'sortable' => true, 'searchable' => true],
                    ['key' => 'records_count', 'label' => 'Catches Logged', 'type' => 'count', 'align' => 'center', 'sortable' => true, 'sortKey' => 'records_count'],
                ],
                'searchPlaceholder' => 'Filter lakes in this zone...',
                'itemName' => 'lakes',
                'perPage' => 10,
            ])

        @else
            <div class="py-8 text-center text-slate-400 text-xs italic space-y-2">
                <x-lucide-info class="w-8 h-8 text-slate-300 mx-auto" />
                <p>No registered lakes currently tagged in {{ $fishingZone->code }}.</p>
                <a href="{{ url('/lake/create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:underline">
                    <x-lucide-plus class="w-3.5 h-3.5" />
                    <span>Register New Lake & Tag {{ $fishingZone->code }}</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
