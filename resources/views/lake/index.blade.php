@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="lake" />

        <!-- Lakes Data Table with Unified Server Search, Multi-Sort & Column Controls -->
        <div x-data="dataTable({ defaultDensity: 'normal' })">
            <x-table.wrapper 
                searchPlaceholder="Search lakes by name..." 
                itemName="lakes"
                :totalCount="$lakes->total()"
                :showColumnPicker="true"
                :showDensity="true"
            >
                <x-slot:extraFilters>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">Fish Count:</span>
                        <div class="flex items-center">
                            <select name="records_count_operator" onchange="this.form.submit()" class="h-8 pl-2.5 pr-6 text-xs rounded-l-lg border border-r-0 border-slate-200 bg-slate-50 font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                                <option value=">" {{ request('records_count_operator') === ">" ? "selected" : ""}}>&gt;</option>
                                <option value="=" {{ request('records_count_operator') === "=" ? "selected" : ""}}>=</option>
                                <option value="<" {{ request('records_count_operator') === "<" ? "selected" : ""}}>&lt;</option>
                            </select>
                            <input name="records_count" type="number" value="{{ request('records_count') }}" placeholder="Count..." onchange="this.form.submit()" class="h-8 px-2.5 w-20 text-xs rounded-r-lg border border-slate-200 bg-white font-mono text-slate-800 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500" />
                        </div>
                    </div>
                </x-slot:extraFilters>

                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <x-table.th col="name" type="text" label="Lake Name">Lake Name</x-table.th>
                            <x-table.th col="gps" align="center" label="GPS Location">GPS</x-table.th>
                            <x-table.th col="catches" type="number" align="center" label="Catches">Catches</x-table.th>
                            <x-table.th col="visits" type="number" align="center" label="Visits">Visits</x-table.th>
                            <x-table.th col="rate" type="number" align="center" label="Catches/Visit">Catches/Visit</x-table.th>
                            <x-table.th col="anglers" type="number" align="center" label="Anglers">Anglers</x-table.th>
                            <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                        @foreach($lakes as $lake)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td data-col="name" x-show="isColumnVisible('name')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-bold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="waves" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                        <div>
                                            <a href="{{ url('/lake/' . $lake->id) }}" class="hover:text-teal-600 hover:underline block leading-tight font-bold text-slate-900">
                                                {{ $lake->name }}
                                            </a>
                                            @if($lake->fishingZone)
                                                <a href="{{ url('/fishing-zone/' . $lake->fishingZone->id) }}" class="text-[10px] font-mono font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100 hover:bg-indigo-100 inline-block mt-0.5">
                                                    {{ $lake->fishingZone->code }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td data-col="gps" x-show="isColumnVisible('gps')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center">
                                    @if($lake->latitude && $lake->longitude)
                                        <a href="{{ url('/lake/' . $lake->id) }}" 
                                           title="GPS Coordinates Mapped: {{ number_format($lake->latitude, 4) }}°N, {{ number_format($lake->longitude, 4) }}°W" 
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 transition-colors border border-emerald-200/80 shadow-2xs">
                                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                                        </a>
                                    @else
                                        <a href="{{ url('/lake/' . $lake->id) }}" 
                                           title="GPS Unmapped — Click to view lake details" 
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors border border-slate-200/70">
                                            <i data-lucide="map-pin-off" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                </td>

                                <td data-col="catches" x-show="isColumnVisible('catches')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-teal-700">{{ $lake->records_count }}</td>
                                <td data-col="visits" x-show="isColumnVisible('visits')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-800">{{ $lake->visits }}</td>
                                <td data-col="rate" x-show="isColumnVisible('rate')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-medium text-emerald-700">
                                    @if($lake->visits > 0) {{ round($lake->records_count/$lake->visits, 2) }} @else — @endif
                                </td>
                                <td data-col="anglers" x-show="isColumnVisible('anglers')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-medium text-sky-700">{{ $lake->anglers_count }}</td>
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
