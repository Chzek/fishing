@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="record" />

        <!-- Filters form -->
        <form class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Filter Length</span>
                <input id="length" name="length" type="number" step="0.25"
                    @if(Request::input('length', false))
                        value='{{ Request::input('length') }}'
                    @endif
                    placeholder="Inches..."
                    class="h-9 px-3 w-28 text-xs rounded-xl border border-slate-200 bg-white font-mono text-slate-800 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500"
                />

                <select name="length_operator" class="h-9 px-3 text-xs rounded-xl border border-slate-200 bg-white font-bold text-slate-700 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    <option value=">" {{ Request::input('length_operator') === ">" ? "selected" : ""}} >&gt; (Greater than)</option>
                    <option value="=" {{ Request::input('length_operator') === "=" ? "selected" : ""}} >= (Equal)</option>
                    <option value="<" {{ Request::input('length_operator') === "<" ? "selected" : ""}} >&lt; (Less than)</option>
                </select>
            </div>

            <button type="submit" class="h-9 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Apply Filter</span>
            </button>
        </form>

        <!-- Records Data Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th scope="col" class="py-3 px-4">Date</th>
                        <th scope="col" class="py-3 px-4">Angler</th>
                        <th scope="col" class="py-3 px-4">Lake / Water</th>
                        <th scope="col" class="py-3 px-4">Fish Species</th>
                        <th scope="col" class="py-3 px-4">Lure / Bait</th>
                        <th scope="col" class="py-3 px-4 text-center">Weight (lbs)</th>
                        <th scope="col" class="py-3 px-4 text-center">Length (in)</th>
                        <th scope="col" class="py-3 px-4 text-center">Water Temp</th>
                        <th scope="col" class="py-3 px-4 text-center">Weather Telemetry</th>
                        <th scope="col" class="py-3 px-4">Status</th>
                        <th scope="col" class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($records as $record)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-medium text-slate-900 whitespace-nowrap">{{ $record->caught }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800 whitespace-nowrap">{{ $record->angler->full_name }}</td>
                            <td class="py-3.5 px-4 text-slate-700 whitespace-nowrap">{{ $record->lake->name }}</td>
                            <td class="py-3.5 px-4 font-bold text-teal-700 whitespace-nowrap">{{ $record->fishBreed->name }}</td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                @if($record->lure)
                                    <span title="{{ $record->lure->displayName }}">{{ \Illuminate\Support\Str::limit($record->lure->displayName, 20) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-medium">{{ $record->weight ? number_format($record->weight, 2) : '—' }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-medium">{{ $record->length ? number_format($record->length, 2) : '—' }}</td>
                            <td class="py-3.5 px-4 text-center text-xs font-mono font-medium text-slate-600">
                                {{ $record->temperature ? $record->temperature . '°F' : '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($record->dailyWeather)
                                    <span class="inline-flex flex-col items-center bg-slate-50 border border-slate-200 rounded-lg px-2 py-1 text-[11px] text-slate-700" title="{{ $record->dailyWeather->weather_condition }} ({{ $record->dailyWeather->air_temp_min }}°F – {{ $record->dailyWeather->air_temp_max }}°F)">
                                        <span class="font-semibold">{{ $record->dailyWeather->weather_condition }}</span>
                                        <span class="text-[10px] text-slate-500 font-mono">{{ $record->dailyWeather->air_temp_mean }}°F | {{ $record->dailyWeather->barometric_pressure }}hPa</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($record->released == 1)
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-emerald-200">
                                        Released
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-sky-50 text-sky-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-sky-200">
                                        Kept
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <x-tableOptions name='record' identifier='{{ $record->id }}' />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-500 pt-2">
            <span>Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }} Records</span>
            <div>{{ $records->links() }}</div>
        </div>
    </div>
</div>
@endsection
