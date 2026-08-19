@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- 1. Header Hero Banner -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 space-y-4">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ url('/record') }}" class="w-10 h-10 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 flex items-center justify-center transition-colors shrink-0" title="Return to Catches Telemetry Dashboard">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2.5">
                        <span>Catches Logbook Directory</span>
                        <span class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">{{ number_format($totalCount) }} Total Records</span>
                    </h1>
                    <p class="text-xs text-slate-400 font-medium pt-0.5">Search, filter, and inspect individual catch records, weather telemetry, and lure logs</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ url('/record') }}" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                    <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-teal-400"></i>
                    <span>Telemetry Dashboard</span>
                </a>
                <a href="{{ url('/record/create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-950/50 transition-all flex items-center gap-1.5">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Log New Catch</span>
                </a>
                <a href="{{ url('/record/quick') }}" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-950/50 transition-all flex items-center gap-1.5">
                    <i data-lucide="zap" class="w-4 h-4 text-emerald-200"></i>
                    <span>Boat Quick Catch</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Logbook Catches Directory & Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-teal-600"></i>
                    <span>Catch Records Directory</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Search and filter individual catch records with instant multi-sort and local filtering</p>
            </div>

            <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                Showing {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} of {{ $records->total() }} Records
            </span>
        </div>

        <!-- Server Filters form -->
        <form action="{{ url('/record/directory') }}" method="GET" class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
                <!-- Unified Keyword Search -->
                <div class="relative flex-1 flex items-center min-w-[180px]">
                    <i data-lucide="database" class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none shrink-0"></i>
                    <input type="text" name="search" value="{{ Request::input('search') }}" placeholder="Server query (species, lake, angler)..."
                        class="w-full h-9 pl-10 pr-3 text-xs rounded-xl border border-slate-200 bg-white text-slate-800 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>

                <!-- Streamlined Length Numeric Filter -->
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Length</span>
                    <select name="length_operator" class="h-9 px-2 text-xs rounded-xl border border-slate-200 bg-white font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20">
                        <option value=">" {{ Request::input('length_operator') === '>' ? 'selected' : '' }}>&gt;</option>
                        <option value="=" {{ Request::input('length_operator') === '=' ? 'selected' : '' }}>=</option>
                        <option value="<" {{ Request::input('length_operator') === '<' ? 'selected' : '' }}>&lt;</option>
                    </select>
                    <input type="number" step="0.25" name="length" value="{{ Request::input('length') }}" placeholder="Inches..."
                        class="h-9 px-3 w-24 text-xs rounded-xl border border-slate-200 bg-white font-mono text-slate-800 focus:ring-2 focus:ring-teal-500/20">
                </div>

                @if(Request::has('angler'))
                    <input type="hidden" name="angler" value="{{ Request::input('angler') }}">
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="h-9 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Query Database</span>
                </button>
                @if(Request::hasAny(['search', 'length', 'length_operator', 'angler']))
                    <a href="{{ url('/record/directory') }}" class="h-9 px-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs rounded-xl transition-colors flex items-center gap-1">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        <!-- Records Data Table with Local Search, Multi-Sort, Density & Column Picker -->
        <div x-data="dataTable({ defaultDensity: 'normal' })">
            <x-table.wrapper 
                searchPlaceholder="Instant filter on loaded catches..." 
                itemName="catches"
                :showColumnPicker="true"
                :showDensity="true"
            >
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <x-table.th col="date" type="date" label="Date">Date</x-table.th>
                            <x-table.th col="angler" type="text" label="Angler">Angler</x-table.th>
                            <x-table.th col="lake" type="text" label="Lake">Lake / Water</x-table.th>
                            <x-table.th col="species" type="text" label="Species">Fish Species</x-table.th>
                            <x-table.th col="lure" type="text" label="Lure">Lure / Bait</x-table.th>
                            <x-table.th col="weight" type="number" align="center" label="Weight">Weight (lbs)</x-table.th>
                            <x-table.th col="length" type="number" align="center" label="Length">Length (in)</x-table.th>
                            <x-table.th col="status" type="text" align="center" label="Status">Status</x-table.th>
                            <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                        @forelse($records as $record)
                            <tr data-table-row class="hover:bg-slate-50/70 transition-colors">
                                <td data-col="date" data-sort-val="{{ $record->caught }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-medium text-slate-900 whitespace-nowrap font-mono text-xs">{{ $record->caught }}</td>
                                <td data-col="angler" data-sort-val="{{ $record->angler->full_name }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-semibold text-slate-800 whitespace-nowrap">
                                    <a href="{{ url('/angler/' . $record->angler->id . '/profile') }}" class="hover:text-teal-600 hover:underline">
                                        {{ $record->angler->full_name }}
                                    </a>
                                </td>
                                <td data-col="lake" data-sort-val="{{ $record->lake->name }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-slate-700 whitespace-nowrap">
                                    <a href="{{ url('/lake/' . $record->lake->id) }}" class="hover:text-teal-600 hover:underline">
                                        {{ $record->lake->name }}
                                    </a>
                                </td>
                                <td data-col="species" data-sort-val="{{ $record->fishBreed->name }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-bold text-teal-700 whitespace-nowrap">{{ $record->fishBreed->name }}</td>
                                <td data-col="lure" data-sort-val="{{ $record->lure->name ?? '' }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-xs text-slate-600">
                                    @if($record->lure)
                                        <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium border border-slate-200">
                                            {{ $record->lure->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td data-col="weight" data-sort-val="{{ $record->weight ?? 0 }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-900">{{ $record->weight ? $record->weight . ' lbs' : '—' }}</td>
                                <td data-col="length" data-sort-val="{{ $record->length ?? 0 }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-teal-700">{{ $record->length ? $record->length . '″' : '—' }}</td>
                                <td data-col="status" data-sort-val="{{ $record->released ? 'Released' : 'Kept' }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center">
                                    @if($record->released)
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold px-2 py-0.5 rounded-full font-mono">Released</span>
                                    @else
                                        <span class="bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-medium px-2 py-0.5 rounded-full font-mono">Kept</span>
                                    @endif
                                </td>
                                <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ url('/record/' . $record->id) }}" class="p-1.5 hover:bg-slate-100 text-slate-600 hover:text-teal-600 rounded-lg transition-colors" title="View Details">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ url('/record/' . $record->id . '/edit') }}" class="p-1.5 hover:bg-slate-100 text-slate-600 hover:text-amber-600 rounded-lg transition-colors" title="Edit Catch">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400 italic text-xs">
                                    No records found matching the filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!-- Dynamic Summary Footer for Visible Filtered Rows -->
                    @if($records->isNotEmpty())
                        <tfoot class="bg-slate-50 text-xs font-semibold text-slate-600 border-t border-slate-200">
                            <tr>
                                <td colspan="5" class="py-2.5 px-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">
                                    Live Visible Summary
                                </td>
                                <td data-col="weight" class="py-2.5 px-4 text-center font-mono font-bold text-slate-800">
                                    Avg: <span data-aggregate-for="weight" data-aggregate-type="avg">—</span> lbs
                                </td>
                                <td data-col="length" class="py-2.5 px-4 text-center font-mono font-bold text-teal-700">
                                    Avg: <span data-aggregate-for="length" data-aggregate-type="avg">—</span>″
                                </td>
                                <td colspan="2" class="py-2.5 px-4"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </x-table.wrapper>
        </div>

        @if($records->hasPages())
            <div class="pt-2">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
