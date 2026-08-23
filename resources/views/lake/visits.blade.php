@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $lake->name }} Visits Log</h1>
                    <p class="text-xs text-slate-500">Historical trip visit records</p>
                </div>
            </div>
            <a href="/lake/{{ $lake->id }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">Back to Lake</a>
        </div>

        <div class="space-y-6">
            @foreach($recordsByDate as $records)
                <div class="border border-slate-200/80 rounded-2xl p-4 bg-slate-50/50 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                        <i data-lucide="clock" class="w-4 h-4 text-teal-600"></i>
                        <span>Visit Date: {{ $records[0]->caught }}</span>
                        <span class="text-slate-400">({{ count($records) }} Fish Logged)</span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200/80 bg-white">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                                <tr>
                                    <th scope="col" class="py-2.5 px-3">Angler</th>
                                    <th scope="col" class="py-2.5 px-3">Species</th>
                                    <th scope="col" class="py-2.5 px-3">Lure</th>
                                    <th scope="col" class="py-2.5 px-3 text-center">Weight (lbs)</th>
                                    <th scope="col" class="py-2.5 px-3 text-center">Length (in)</th>
                                    <th scope="col" class="py-2.5 px-3 text-center">Water Temp</th>
                                    <th scope="col" class="py-2.5 px-3">Status</th>
                                    <th scope="col" class="py-2.5 px-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($records as $record)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $record->angler->lastName }}, {{ $record->angler->firstName }}</td>
                                        <td class="py-2.5 px-3 font-bold text-teal-700">{{ $record->fishBreed->name }}</td>
                                        <td class="py-2.5 px-3 text-slate-600">
                                            @if($record->lure)
                                                <span title="{{ $record->lure->displayName }}">{{ \Illuminate\Support\Str::limit($record->lure->displayName, 20) }}</span>
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-mono">{{ $record->weight ?? '—' }}</td>
                                        <td class="py-2.5 px-3 text-center font-mono">{{ $record->length ?? '—' }}</td>
                                        <td class="py-2.5 px-3 text-center font-mono text-slate-600">{{ $record->temperature ? $record->temperature . '°F' : '—' }}</td>
                                        <td class="py-2.5 px-3">
                                            @if($record->released == 1)
                                                <span class="inline-flex items-center bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-amber-200">Released</span>
                                            @else
                                                <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">Kept</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                            <x-tableOptions name='record' identifier='{{ $record->id }}' />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
