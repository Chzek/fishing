@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Tackle Item Hero Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 sm:p-7 border border-slate-800 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="target" class="w-7 h-7"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-mono font-bold uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-500/30 px-2.5 py-0.5 rounded-lg">
                        {{ $lure->category ?: 'Tackle Item' }}
                    </span>
                    @if($lure->brand)
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider font-mono">• {{ $lure->brand }}</span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">{{ $lure->name }}</h1>
                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400 font-mono mt-1">
                    @if($lure->color)
                        <span class="text-amber-300 font-bold bg-amber-500/20 border border-amber-500/30 px-2 py-0.5 rounded">{{ $lure->color }}</span>
                    @endif
                    @if($lure->size || $lure->weight)
                        <span>Weight: <strong class="text-white">{{ $lure->size ?: $lure->weight }}</strong></span>
                    @endif
                    @if($lure->depth_range)
                        <span>•</span>
                        <span>Depth: <strong class="text-sky-300">{{ $lure->depth_range }}</strong></span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="/lure/{{ $lure->id }}/edit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <i data-lucide="edit-3" class="w-4 h-4 text-teal-400"></i>
                <span>Edit Specs</span>
            </a>
            <a href="/lure" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                Back to Tackle Box
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-4 rounded-xl shadow-sm flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Telemetry Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <x-kpiMetric label="Total Catches Landed" :value="$lure->records_count" icon="hook" color="teal" subtext="Verified Logbook Catches" />
        <x-kpiMetric label="Top Target Species" :value="$topSpeciesName" icon="fish" color="emerald" subtext="Most Landed Species" />
        <x-kpiMetric label="Lure Category" :value="$lure->category ?: 'Other'" icon="layers" color="sky" subtext="{{ $lure->brand ?: 'Generic Brand' }}" />
    </div>

    <!-- Catches Landed Feed (Powered by x-catchCard) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-teal-600"></i>
                <span>Catches Landed Using {{ $lure->displayName }}</span>
            </h2>
            <span class="text-xs text-slate-400 font-mono">Showing {{ $catches->total() }} Logged Entry(ies)</span>
        </div>

        @if($catches->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($catches as $catch)
                    <x-catchCard :record="$catch" />
                @endforeach
            </div>

            @if($catches->hasPages())
                <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                    <span class="text-xs text-slate-500">Showing {{ $catches->firstItem() }} to {{ $catches->lastItem() }} of {{ $catches->total() }} Catches</span>
                    <div>{{ $catches->links() }}</div>
                </div>
            @endif
        @else
            <x-emptyState icon="fish-off" title="No Catches Logged on this Lure Yet" description="Log a catch using this lure from the boat or field logbook!" actionUrl="/record/quick" actionLabel="Log Catch Now" />
        @endif
    </div>
</div>
@endsection
