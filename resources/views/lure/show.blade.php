@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Tackle Item Hero Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 sm:p-7 border border-slate-800 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <x-lucide-target class="w-7 h-7" />
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

        <div class="flex flex-wrap items-center gap-2">
            <a href="/record/directory?lure_id={{ $lure->id }}" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
                <x-lucide-external-link class="w-3.5 h-3.5" />
                <span>Directory Filter</span>
            </a>
            <a href="/lure/{{ $lure->id }}/edit" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <x-lucide-edit-3 class="w-3.5 h-3.5 text-teal-400" />
                <span>Edit Specs</span>
            </a>
            <a href="/lure" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                Back to Tackle Box
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-4 rounded-xl shadow-sm flex items-center gap-2">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Telemetry Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <x-kpiMetric label="Total Catches Landed" :value="$lure->records_count" icon="fishing-hook" color="teal" subtext="Verified Logbook Catches" />
        <x-kpiMetric label="Top Target Species" :value="$topSpeciesName" icon="fish" color="emerald" subtext="Most Landed Species" />
        <x-kpiMetric label="Lure Category" :value="$lure->category ?: 'Other'" icon="layers" color="sky" subtext="{{ $lure->brand ?: 'Generic Brand' }}" />
    </div>

    <!-- Compact Catches Log Table View -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <x-lucide-history class="w-4 h-4 text-teal-600" />
                    <span>Catches Landed Using {{ $lure->displayName }}</span>
                </h2>
                <p class="text-xs text-slate-500">High-density logbook table view for tackle telemetry analysis</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="/record/directory?lure_id={{ $lure->id }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition-colors flex items-center gap-1">
                    <x-lucide-filter class="w-3.5 h-3.5 text-teal-600" />
                    <span>View in Catches Directory →</span>
                </a>
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
                            <th scope="col" class="py-3 px-4">Waterbody / Lake</th>
                            <th scope="col" class="py-3 px-4 text-center">Length / Weight</th>
                            <th scope="col" class="py-3 px-4 text-center">Water Temp</th>
                            <th scope="col" class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($catches as $catch)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                    {{ $catch->caught ? \Carbon\Carbon::parse($catch->caught)->format('M j, Y') : '—' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($catch->angler)
                                        <div class="flex items-center gap-2">
                                            <x-anglerAvatar :angler="$catch->angler" size="sm" />
                                            <a href="/angler/{{ $catch->angler->id }}/profile" class="font-bold text-slate-900 hover:text-teal-600">
                                                {{ $catch->angler->fullName }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-slate-400">Unknown</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($catch->fishBreed)
                                        <a href="/fish/{{ $catch->fishBreed->id }}" class="inline-flex items-center gap-1.5 font-bold text-teal-700 hover:underline">
                                            <x-lucide-fish class="w-3.5 h-3.5 text-teal-600" />
                                            <span>{{ $catch->fishBreed->name }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400">Unspecified</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">
                                    @if($catch->lake)
                                        <a href="/lake/{{ $catch->lake->id }}" class="hover:text-teal-600 font-semibold">
                                            {{ $catch->lake->name }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-900">
                                    {{ $catch->length ? number_format($catch->length, 1) . '"' : '—' }}
                                    @if($catch->weight)
                                        <span class="text-slate-400 font-normal"> / {{ number_format($catch->weight, 1) }} lbs.</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-sky-700 font-semibold">
                                    {{ $catch->temperature ? round($catch->temperature) . '°F' : '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="/record/show/{{ $catch->id }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-teal-50 hover:text-teal-700 text-slate-700 font-bold text-[11px] rounded-lg border border-slate-200 transition-colors inline-flex items-center gap-1">
                                        <span>Dossier</span>
                                        <x-lucide-arrow-right class="w-3 h-3" />
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
            <x-emptyState icon="fish-off" title="No Catches Logged on this Lure Yet" description="Log a catch using this lure from the boat or field logbook!" actionUrl="/record/quick" actionLabel="Log Catch Now" />
        @endif
    </div>
</div>
@endsection
