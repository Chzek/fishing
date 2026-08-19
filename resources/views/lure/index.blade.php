@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="lure" />

        <div x-data="dataTable({ defaultDensity: 'normal' })">
            <x-table.wrapper 
                searchPlaceholder="Quick filter lures by name, color, or size..." 
                itemName="lures"
                :showColumnPicker="false"
                :showDensity="true"
            >
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <x-table.th col="id" type="number" label="ID">ID</x-table.th>
                            <x-table.th col="name" type="text" label="Lure Name">Lure Name</x-table.th>
                            <x-table.th col="color" type="text" label="Color">Color</x-table.th>
                            <x-table.th col="size" type="text" label="Size">Size</x-table.th>
                            <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                        @foreach($lures as $lure)
                            <tr data-table-row class="hover:bg-slate-50/70 transition-colors">
                                <td data-col="id" data-sort-val="{{ $lure->id }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-mono text-xs text-slate-400">#{{ $lure->id }}</td>
                                <td data-col="name" data-sort-val="{{ $lure->name }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="font-bold text-slate-900 flex items-center gap-2">
                                    <i data-lucide="fishing-hook" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                    <span>{{ $lure->name }}</span>
                                </td>
                                <td data-col="color" data-sort-val="{{ $lure->color }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-slate-700 font-medium">{{ $lure->color }}</td>
                                <td data-col="size" data-sort-val="{{ $lure->size }}" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-slate-600 font-mono text-xs">{{ $lure->size }}</td>
                                <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-right whitespace-nowrap">
                                    <x-tableOptions name='lure' identifier='{{ $lure->id }}' />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-table.wrapper>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span>Showing {{ $lures->firstItem() }} to {{ $lures->lastItem() }} of {{ $lures->total() }} Lures</span>
            <div>{{ $lures->links() }}</div>
        </div>
    </div>
</div>
@endsection
