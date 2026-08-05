@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-5">
        <x-pageNavigation name="angler" />

        @if(count($anglers) > 0)
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <th scope="col" class="py-3 px-4">Last Name</th>
                            <th scope="col" class="py-3 px-4">First Name</th>
                            <th scope="col" class="py-3 px-4">Middle Name</th>
                            <th scope="col" class="py-3 px-4 text-center">Catches</th>
                            <th scope="col" class="py-3 px-4 text-center">Lakes Visited</th>
                            <th scope="col" class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($anglers as $angler)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ $angler->lastName }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $angler->firstName }}</td>
                                <td class="py-3.5 px-4 text-slate-500">{{ $angler->middleName ?? '—' }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-teal-700">{{ $angler->records_count }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800">{{ $angler->lakes_count }}</td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <x-tableOptions name='angler' identifier='{{ $angler->id }}' user='{{ $angler->user_id }}' />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between text-xs text-slate-500 pt-2">
                <span>Showing {{ $anglers->firstItem() }} to {{ $anglers->lastItem() }} of {{ $anglers->total() }} Anglers</span>
                <div>{{ $anglers->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection
