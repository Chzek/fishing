@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="lake" />

        <!-- Filters form -->
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
                <span>Filter Lakes</span>
            </button>
        </form>

        <!-- Lakes Data Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th scope="col" class="py-3 px-4">
                            @if (Request::input('sort_by') === 'name' && Request::input('sort_order') === 'desc')
                                <a href="{{ route('lakes.index', ['sort_by' => 'name', 'sort_order' => 'asc']) }}" class="hover:text-teal-600 flex items-center gap-1">Lake Name ▲</a>    
                            @else
                                <a href="{{ route('lakes.index', ['sort_by' => 'name', 'sort_order' => 'desc']) }}" class="hover:text-teal-600 flex items-center gap-1">Lake Name ▼</a>
                            @endif
                        </th>
                        <th scope="col" class="py-3 px-4 text-center">Latitude (°N)</th>
                        <th scope="col" class="py-3 px-4 text-center">Longitude (°W)</th>
                        <th scope="col" class="py-3 px-4 text-center">
                            @if(Request::input('sort_by') === 'records_count' && Request::input('sort_order') === 'desc')
                                <a href="{{ route('lakes.index', ['sort_by' => 'records_count', 'sort_order' => 'asc']) }}" class="hover:text-teal-600">Catches ▲</a>
                            @else
                                <a href="{{ route('lakes.index', ['sort_by' => 'records_count', 'sort_order' => 'desc']) }}" class="hover:text-teal-600">Catches ▼</a>
                            @endif
                        </th>
                        <th scope="col" class="py-3 px-4 text-center">Visits</th>
                        <th scope="col" class="py-3 px-4 text-center">Catches/Visit</th>
                        <th scope="col" class="py-3 px-4 text-center">Anglers</th>
                        <th scope="col" class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($lakes as $lake)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2">
                                <i data-lucide="waves" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                <span>{{ $lake->name }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-xs font-mono text-slate-600">{{ $lake->latitude ? number_format($lake->latitude, 4) : '—' }}</td>
                            <td class="py-3.5 px-4 text-center text-xs font-mono text-slate-600">{{ $lake->longitude ? number_format($lake->longitude, 4) : '—' }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-teal-700">{{ $lake->records_count }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800">{{ $lake->visits }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-medium text-emerald-700">
                                @if($lake->visits > 0) {{ round($lake->records_count/$lake->visits, 2) }} @else — @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-medium text-sky-700">{{ $lake->anglers_count }}</td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <x-tableOptions name='lake' identifier='{{ $lake->id }}' />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span>Showing {{ $lakes->firstItem() }} to {{ $lakes->lastItem() }} of {{ $lakes->total() }} Lakes</span>
            <div>{{ $lakes->links('vendor.pagination.tailwind') }}</div>
        </div>
    </div>
</div>
@endsection
