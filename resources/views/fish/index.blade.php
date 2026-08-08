@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="fish" class="w-5 h-5 text-teal-600"></i>
                <span>Fish Taxonomy Index</span>
            </h1>
            <div class="flex items-center gap-2">
                <a href="/fish/family/create" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl shadow transition-colors">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Family</span>
                </a>
                <a href="/fish/breed/create" class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Species / Breed</span>
                </a>
                <a href="/" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Return</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200/80">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th scope="col" class="py-3 px-4">Family</th>
                        <th scope="col" class="py-3 px-4">Name (Species / Breed)</th>
                        <th scope="col" class="py-3 px-4 text-center">Total Caught</th>
                        <th scope="col" class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($fishes as $fish)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-slate-600">{{ $fish->family->name }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 flex items-center gap-2">
                                <i data-lucide="fish" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                <span>{{ $fish->name }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-teal-700">{{ $fish->records_count }}</td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <x-tableOptions name='fish' identifier='{{ $fish->id }}' />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span>Showing {{ $fishes->firstItem() }} to {{ $fishes->lastItem() }} of {{ $fishes->total() }} Species</span>
            <div>{{ $fishes->links('vendor.pagination.tailwind') }}</div>
        </div>
    </div>
</div>
@endsection
