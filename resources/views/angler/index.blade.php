@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Sub-navigation Tab Switcher -->
    <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
        <div class="flex items-center gap-2">
            <a href="{{ url('/angler') }}" class="px-4 py-2 text-xs font-bold rounded-xl bg-teal-500/10 text-teal-700 border border-teal-500/20 flex items-center gap-2 shadow-2xs">
                <i data-lucide="users" class="w-4 h-4 text-teal-600"></i>
                <span>Anglers Directory</span>
            </a>
            <a href="{{ url('/angler/stats') }}" class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-slate-400"></i>
                <span>Angler Stats & Summary</span>
            </a>
        </div>

        <a href="{{ url('/angler/create') }}" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
            <span>Add Angler</span>
        </a>
    </div>

    @if(count($anglers) > 0)
        <!-- Anglers Data Table with Unified Server Search, Multi-Sort & Column Controls -->
        <div x-data="dataTable({ defaultDensity: 'normal' })">
            <x-table.wrapper 
                searchPlaceholder="Search anglers by first or last name..." 
                itemName="anglers"
                :totalCount="$anglers->total()"
                :showColumnPicker="true"
                :showDensity="true"
            >
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                        <tr>
                            <x-table.th col="name" type="text" label="Angler">Angler</x-table.th>
                            <x-table.th col="catches" type="number" align="center" label="Catches">Catches</x-table.th>
                            <x-table.th col="lakes" type="number" align="center" label="Lakes Visited">Lakes Visited</x-table.th>
                            <th scope="col" class="py-3 px-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody x-ref="tbody" class="divide-y divide-slate-100 bg-white">
                        @foreach($anglers as $angler)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td data-col="name" x-show="isColumnVisible('name')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ url('/angler/' . $angler->id . '/profile') }}" class="shrink-0">
                                            <x-anglerAvatar :angler="$angler" size="sm" />
                                        </a>
                                        <div>
                                            <a href="{{ url('/angler/' . $angler->id . '/profile') }}" class="font-bold text-slate-900 hover:text-teal-600 hover:underline block leading-tight">
                                                {{ $angler->lastName }}, {{ $angler->firstName }} {{ $angler->middleName ? substr($angler->middleName, 0, 1) . '.' : '' }}
                                            </a>
                                            <span class="text-[11px] text-slate-500 block">Crew Angler</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-col="catches" x-show="isColumnVisible('catches')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-teal-700">{{ $angler->records_count }}</td>
                                <td data-col="lakes" x-show="isColumnVisible('lakes')" :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-center font-mono font-bold text-slate-800">{{ $angler->lakes_count }}</td>
                                <td :class="density === 'compact' ? 'py-2 px-4' : 'py-3.5 px-4'" class="text-right whitespace-nowrap">
                                    <a href="{{ url('/angler/' . $angler->id . '/profile') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-teal-50 text-slate-700 hover:text-teal-700 font-semibold text-xs rounded-xl border border-slate-200 hover:border-teal-200 transition-colors">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-teal-600"></i>
                                        <span>Profile</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-table.wrapper>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 pt-3 border-t border-slate-100">
            <span>Showing {{ $anglers->firstItem() }} to {{ $anglers->lastItem() }} of {{ $anglers->total() }} Anglers</span>
            <div>{{ $anglers->links() }}</div>
        </div>
    @else
        <div class="text-center py-12 px-4 space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-600 border border-teal-500/20 flex items-center justify-center mx-auto">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900">No Anglers Registered Yet</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">Add your fishing crew members to start tracking personal bests and expedition logs.</p>
            <a href="/angler/create" class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs rounded-xl shadow transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add First Angler</span>
            </a>
        </div>
    @endif
</div>
@endsection
