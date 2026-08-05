@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="expedition" />

        <p class="text-xs text-slate-500 leading-relaxed">
            An <strong>expedition</strong> is a group of like-minded anglers gathering to adventure into the wilderness in pursuit of trophy catches and multi-day fishing trips.
        </p>

        <!-- Expeditions Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th scope="col" class="py-3 px-4">Trip Description</th>
                        <th scope="col" class="py-3 px-4">Start Date</th>
                        <th scope="col" class="py-3 px-4">Finish Date</th>
                        <th scope="col" class="py-3 px-4 text-center">Crew Anglers</th>
                        <th scope="col" class="py-3 px-4 text-center">Catches Logged</th>
                        <th scope="col" class="py-3 px-4 text-center">Trip Posts</th>
                        <th scope="col" class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($expeditions as $expedition)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2">
                                <i data-lucide="ship" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                <span>{{ $expedition->description }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-medium text-slate-600 whitespace-nowrap">{{ $expedition->start }}</td>
                            <td class="py-3.5 px-4 text-xs font-medium text-slate-600 whitespace-nowrap">{{ $expedition->finish }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800">{{ $expedition->crews_count }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-teal-700">{{ $expedition->records_count }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-sky-700">{{ $expedition->posts_count }}</td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <x-tableOptions name='expedition' identifier='{{ $expedition->id }}' />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-xs text-slate-500 pt-2">
            Total {{ $expeditions->count() }} Expedition Trip(s)
        </div>
    </div>
</div>
@endsection
