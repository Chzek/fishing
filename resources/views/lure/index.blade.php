@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="lure" />

        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th scope="col" class="py-3 px-4">ID</th>
                        <th scope="col" class="py-3 px-4">Lure Name</th>
                        <th scope="col" class="py-3 px-4">Color</th>
                        <th scope="col" class="py-3 px-4">Size</th>
                        <th scope="col" class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($lures as $lure)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-400">#{{ $lure->id }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2">
                                <i data-lucide="fishing-hook" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                <span>{{ $lure->name }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">{{ $lure->color }}</td>
                            <td class="py-3.5 px-4 text-slate-600 font-mono text-xs">{{ $lure->size }}</td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <x-tableOptions name='lure' identifier='{{ $lure->id }}' />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span>Showing {{ $lures->firstItem() }} to {{ $lures->lastItem() }} of {{ $lures->total() }} Lures</span>
            <div>{{ $lures->links() }}</div>
        </div>
    </div>
</div>
@endsection
