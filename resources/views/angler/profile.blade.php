@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $angler->firstName}} {{ $angler->middleName }} {{ $angler->lastName }}</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Waterbody Performance Summary</p>
        </div>

        @if( count($records) > 0)
            <div class="space-y-6">
                @foreach($records as $key => $group)
                    <div class="border border-slate-200/80 rounded-2xl p-4 bg-slate-50/50 space-y-3">
                        <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i data-lucide="fish" class="w-4 h-4 text-teal-600"></i>
                            <span>{{ $key }}</span>
                        </h2>

                        <div class="overflow-x-auto rounded-xl border border-slate-200/80 bg-white">
                            <table class="w-full text-left text-xs text-slate-700">
                                <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                                    <tr>
                                        <th scope="col" class="py-2.5 px-3">Lake / Water</th>
                                        <th scope="col" class="py-2.5 px-3 text-center">Catches</th>
                                        <th scope="col" class="py-2.5 px-3 text-center">Length (Min / Max / Avg)</th>
                                        <th scope="col" class="py-2.5 px-3 text-center">Weight (Min / Max / Avg)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach( $group as $record)
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $record->lake->name }}</td>
                                            <td class="py-2.5 px-3 text-center font-mono font-bold text-teal-700">{{ $record->catches }}</td>
                                            <td class="py-2.5 px-3 text-center font-mono text-slate-600">{{ $record->min_length }} / {{ $record->max_length }} / {{ $record->avg_length }} in.</td>
                                            <td class="py-2.5 px-3 text-center font-mono text-slate-600">
                                                @if(!is_null($record->min_weight))
                                                    {{ $record->min_weight }} / {{ $record->max_weight }} / {{ $record->avg_weight }} lbs.
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-slate-400 text-xs italic">
                This angler needs to do some more fishing to populate performance stats!
            </div>
        @endif
    </div>
</div>
@endsection
