@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="lake" />

        <!-- Server Filters form -->
        <form class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="flex items-center gap-3">
                <input id="name" name="name" type="text"
                    @if(Request::input('name', false))
                        value='{{ Request::input('name') }}'
                    @endif
                    placeholder="Search Lake Name..."
                    class="h-9 px-3.5 w-48 text-xs rounded-xl border border-slate-200 bg-white font-medium text-slate-800 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
                />

                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Fish Count</span>
                    <input id="records_count" name="records_count" type="number"
                        @if(Request::input('records_count', false))
                            value='{{ Request::input('records_count') }}'
                        @endif
                        placeholder="Count..."
                        class="h-9 px-3 w-24 text-xs rounded-xl border border-slate-200 bg-white font-mono text-slate-800 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
                    />

                    <select name="records_count_operator" class="h-9 px-3 text-xs rounded-xl border border-slate-200 bg-white font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                        <option value=">" {{ Request::input('records_count_operator') === ">" ? "selected" : ""}} >&gt;</option>
                        <option value="=" {{ Request::input('records_count_operator') === "=" ? "selected" : ""}} >=</option>
                        <option value="<" {{ Request::input('records_count_operator') === "<" ? "selected" : ""}} >&lt;</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="h-9 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Query Lakes</span>
            </button>
        </form>

        <!-- Lakes Data Table with Local Search & Multi-Sort -->
        <div x-data="dataTable({ defaultDensity: 'normal' })">
            <x-table.wrapper 
                searchPlaceholder="Quick filter loaded lakes..." 
                itemName="lakes"
                :showColumnPicker="true"
                :showDensity="true"
            >
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <x-table.th col="name" type="text" label="Lake Name">Lake Name</x-table.th>
                            <x-table.th col="lat" type="number" align="center" label="Latitude">Latitude (°N)</x-table.th>
                            <x-table.th col="long" type="number" align="center" label="Longitude">Longitude (°W)</x-table.th>
                            <x-table.th col="catches" type="number" align="center" label="Catches">Catches</x-table.th>
                            <x-table.th col="visits" type="number" align="center" label="Visits">Visits</x-table.th>
                            <x-table.th col="rate" type="number" align="center" label="Catches/Visit">Catches/Visit</x-table.th>
                            <x-table.th col="anglers" type="number" align="center" label="Anglers">Anglers</x-table.th>
                            <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                        @foreach($lakes as $lake)
                            <tr data-table-row class="hover:bg-slate-50/70 transition-colors">
                                <td data-col="name" data-sort-val="{{ $lake->name }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-bold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="waves" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                        <div>
                                            <span class="block leading-tight">{{ $lake->name }}</span>
                                            @if($lake->fishingZone)
                                                <a href="{{ url('/fishing-zone/' . $lake->fishingZone->id) }}" class="text-[10px] font-mono font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100 hover:bg-indigo-100 inline-block mt-0.5">
                                                    {{ $lake->fishingZone->code }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td data-col="lat" data-sort-val="{{ $lake->latitude ?? 0 }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center text-xs font-mono text-slate-600">{{ $lake->latitude ? number_format($lake->latitude, 4) : '—' }}</td>
                                <td data-col="long" data-sort-val="{{ $lake->longitude ?? 0 }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center text-xs font-mono text-slate-600">{{ $lake->longitude ? number_format($lake->longitude, 4) : '—' }}</td>
                                <td data-col="catches" data-sort-val="{{ $lake->records_count }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-teal-700">{{ $lake->records_count }}</td>
                                <td data-col="visits" data-sort-val="{{ $lake->visits }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-800">{{ $lake->visits }}</td>
                                <td data-col="rate" data-sort-val="{{ $lake->visits > 0 ? round($lake->records_count/$lake->visits, 2) : 0 }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-medium text-emerald-700">
                                    @if($lake->visits > 0) {{ round($lake->records_count/$lake->visits, 2) }} @else — @endif
                                </td>
                                <td data-col="anglers" data-sort-val="{{ $lake->anglers_count }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-medium text-sky-700">{{ $lake->anglers_count }}</td>
                                <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-right whitespace-nowrap">
                                    <x-tableOptions name='lake' identifier='{{ $lake->id }}' />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-table.wrapper>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span>Showing {{ $lakes->firstItem() }} to {{ $lakes->lastItem() }} of {{ $lakes->total() }} Lakes</span>
            <div>{{ $lakes->links() }}</div>
        </div>
    </div>
</div>
@endsection
