@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="expedition" />

        <p class="text-xs text-slate-500 leading-relaxed">
            An <strong>expedition</strong> is a group of like-minded anglers gathering to adventure into the wilderness in pursuit of trophy catches and multi-day fishing trips.
        </p>

        <!-- Expeditions Table with Local Filter, Multi-Sort & Density -->
        <div x-data="dataTable({ defaultDensity: 'normal' })">
            <x-table.wrapper 
                searchPlaceholder="Quick filter expeditions by description or date..." 
                itemName="expeditions"
                :showColumnPicker="false"
                :showDensity="true"
            >
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <x-table.th col="desc" type="text" label="Trip Description">Trip Description</x-table.th>
                            <x-table.th col="start" type="date" label="Start Date">Start Date</x-table.th>
                            <x-table.th col="finish" type="date" label="Finish Date">Finish Date</x-table.th>
                            <x-table.th col="crew" type="number" align="center" label="Crew Anglers">Crew Anglers</x-table.th>
                            <x-table.th col="catches" type="number" align="center" label="Catches Logged">Catches Logged</x-table.th>
                            <x-table.th col="posts" type="number" align="center" label="Trip Posts">Trip Posts</x-table.th>
                            <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                        @foreach($expeditions as $expedition)
                            <tr data-table-row class="hover:bg-slate-50/70 transition-colors">
                                <td data-col="desc" data-sort-val="{{ $expedition->description }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-bold text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="ship" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                        <span>{{ $expedition->description }}</span>
                                    </div>
                                </td>
                                <td data-col="start" data-sort-val="{{ $expedition->start }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-xs font-medium text-slate-600 whitespace-nowrap">{{ $expedition->start }}</td>
                                <td data-col="finish" data-sort-val="{{ $expedition->finish }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-xs font-medium text-slate-600 whitespace-nowrap">{{ $expedition->finish }}</td>
                                <td data-col="crew" data-sort-val="{{ $expedition->crews_count }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-800">{{ $expedition->crews_count }}</td>
                                <td data-col="catches" data-sort-val="{{ $expedition->records_count }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-teal-700">{{ $expedition->records_count }}</td>
                                <td data-col="posts" data-sort-val="{{ $expedition->posts_count }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-sky-700">{{ $expedition->posts_count }}</td>
                                <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-right whitespace-nowrap">
                                    <x-tableOptions name='expedition' identifier='{{ $expedition->id }}' />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-table.wrapper>
        </div>

        <div class="text-xs text-slate-500 pt-2">
            Total {{ $expeditions->count() }} Expedition Trip(s)
        </div>
    </div>
</div>
@endsection
