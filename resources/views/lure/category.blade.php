@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Category Hero Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 sm:p-7 border border-slate-800 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="layers" class="w-7 h-7"></i>
            </div>
            <div>
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-500/30 px-2.5 py-0.5 rounded-lg">
                    Category Telemetry
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-1">{{ $category }} Tray</h1>
                <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $totalVariants }} Tackle Item(s) & Variants Registered</p>
            </div>
        </div>

        <a href="/lure" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
            Back to Tackle Box
        </a>
    </div>

    <!-- Category Telemetry KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <x-kpiMetric label="Total Category Catches" :value="$totalCatches" icon="hook" color="teal" subtext="Verified Logbook Catches" />
        <x-kpiMetric label="Top Target Species" :value="$topSpeciesName" icon="fish" color="emerald" subtext="Most Landed Species" />
        <x-kpiMetric label="Top Color Pattern" :value="$topColorName" icon="target" color="sky" subtext="Most Strike-Productive Color" />
    </div>

    <!-- Lure Models in this Category -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="box" class="w-4 h-4 text-teal-600"></i>
                <span>Lure Models in {{ $category }} Tray</span>
            </h2>
            <span class="text-xs text-slate-400 font-mono">{{ $modelsGroup->count() }} Model(s)</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($modelsGroup as $modelName => $variants)
                @php
                    $modelBrand = $variants->first()->brand;
                    $modelCatches = $variants->sum('records_count');
                @endphp
                <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-4 flex items-center justify-between gap-3 hover:border-teal-300 transition-colors">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h3 class="font-bold text-slate-900 text-sm">{{ $modelName }}</h3>
                            @if($modelBrand)
                                <span class="text-[10px] font-bold text-slate-500 font-mono">({{ $modelBrand }})</span>
                            @endif
                        </div>
                        <span class="text-xs text-slate-500 font-mono block mt-0.5">
                            {{ $variants->count() }} Variant(s) • <strong class="text-teal-700">{{ $modelCatches }} catch{{ $modelCatches === 1 ? '' : 'es' }}</strong>
                        </span>
                    </div>
                    <a href="/lure/model/{{ urlencode($modelName) }}" class="px-2.5 py-1 bg-white border border-slate-200 text-teal-600 hover:text-teal-700 font-bold text-xs rounded-lg shadow-2xs transition-colors shrink-0">
                        Model Telemetry →
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Catches Logbook Table for this Category -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-teal-600"></i>
                    <span>Catches Landed on {{ $category }} Lures</span>
                </h2>
                <p class="text-xs text-slate-500">Logbook entries logged using any {{ $category }} lure</p>
            </div>
        </div>

        @if($catches->count() > 0)
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th scope="col" class="py-3 px-4">Date Landed</th>
                            <th scope="col" class="py-3 px-4">Angler</th>
                            <th scope="col" class="py-3 px-4">Species</th>
                            <th scope="col" class="py-3 px-4">Waterbody</th>
                            <th scope="col" class="py-3 px-4 text-center">Length / Weight</th>
                            <th scope="col" class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($catches as $catch)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    {{ $catch->caught ? \Carbon\Carbon::parse($catch->caught)->format('M j, Y') : '—' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $catch->angler?->fullName ?? 'Unknown' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-teal-700">
                                    {{ $catch->fishBreed?->name ?? 'Unspecified' }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">
                                    {{ $catch->lake?->name ?? '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-900">
                                    {{ $catch->length ? number_format($catch->length, 1) . '"' : '—' }}
                                    @if($catch->weight)
                                        <span class="text-slate-400 font-normal"> / {{ number_format($catch->weight, 1) }} lbs.</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="/record/show/{{ $catch->id }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-teal-50 hover:text-teal-700 text-slate-700 font-bold text-[11px] rounded-lg border border-slate-200 transition-colors inline-flex items-center gap-1">
                                        <span>Dossier</span>
                                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($catches->hasPages())
                <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                    <span class="text-xs text-slate-500">Showing {{ $catches->firstItem() }} to {{ $catches->lastItem() }} of {{ $catches->total() }} Catches</span>
                    <div>{{ $catches->links() }}</div>
                </div>
            @endif
        @else
            <x-emptyState icon="fish-off" title="No Catches Logged for this Category" description="Log a catch using a {{ $category }} lure!" actionUrl="/record/quick" actionLabel="Log Catch Now" />
        @endif
    </div>
</div>
@endsection
