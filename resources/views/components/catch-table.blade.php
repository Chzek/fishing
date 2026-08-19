@props(['records'])

<div x-data="dataTable({ defaultDensity: 'normal' })" class="space-y-3">
    <x-table.wrapper 
        searchPlaceholder="Search catches in database..." 
        itemName="catches"
        :totalCount="method_exists($records, 'total') ? $records->total() : count($records)"
        :showColumnPicker="false"
        :showDensity="true"
    >
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <x-table.th col="date" type="date" label="Date">Date</x-table.th>
                    <x-table.th col="angler" type="text" label="Angler">Angler</x-table.th>
                    <x-table.th col="lake" type="text" label="Lake">Lake</x-table.th>
                    <x-table.th col="species" type="text" label="Species">Fish Species</x-table.th>
                    <x-table.th col="length" type="number" align="center" label="Length">Length (in)</x-table.th>
                    <x-table.th col="weight" type="number" align="center" label="Weight">Weight (lbs)</x-table.th>
                    <x-table.th col="lure" type="text" label="Lure">Lure</x-table.th>
                    <x-table.th col="status" type="text" align="center" label="Status">Status</x-table.th>
                </tr>
            </thead>
            <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                @forelse($records as $record)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td data-col="date" :class="density === 'compact' ? 'py-2 px-3' : 'py-3 px-4'" class="font-mono text-slate-900 whitespace-nowrap">{{ $record->caught }}</td>
                        <td data-col="angler" :class="density === 'compact' ? 'py-2 px-3' : 'py-3 px-4'" class="font-semibold text-slate-800">{{ optional($record->angler)->fullName ?? 'N/A' }}</td>
                        <td data-col="lake" :class="density === 'compact' ? 'py-2 px-3' : 'py-3 px-4'" class="text-slate-700">{{ optional($record->lake)->name ?? 'N/A' }}</td>
                        <td data-col="species" :class="density === 'compact' ? 'py-2 px-3' : 'py-3 px-4'" class="font-bold text-teal-700">{{ optional($record->fishBreed)->name ?? 'N/A' }}</td>
                        <td data-col="length" :class="density === 'compact' ? 'py-2 px-3' : 'py-3 px-4'" class="text-center font-mono font-bold text-teal-700">{{ $record->length ? $record->length . '″' : '—' }}</td>
                        <td data-col="weight" :class="density === 'compact' ? 'py-2 px-3' : 'py-3 px-4'" class="text-center font-mono font-bold text-slate-900">{{ $record->weight ? $record->weight . ' lbs' : '—' }}</td>
                        <td data-col="lure" :class="density === 'compact' ? 'py-2 px-3' : 'py-3 px-4'" class="text-slate-600">{{ optional($record->lure)->displayName ?? '—' }}</td>
                        <td data-col="status" :class="density === 'compact' ? 'py-2 px-3' : 'py-3 px-4'" class="text-center">
                            @if($record->released)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Released</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Kept</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-slate-400 py-6 italic text-xs">
                            No catches recorded.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table.wrapper>
</div>

