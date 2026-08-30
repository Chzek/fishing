@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Search Header Bar -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
        <form action="{{ route('search') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 flex items-center w-full">
                <x-lucide-search class="w-5 h-5 text-slate-400 absolute left-3.5 pointer-events-none shrink-0" />
                <input type="text" name="q" value="{{ $query }}" placeholder="Type a command or search term (e.g., 'New Angler', 'Wawa', 'Walleye')..."
                    class="w-full h-11 pl-12 pr-4 text-sm rounded-xl border border-slate-200 bg-slate-50/50 text-slate-900 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 font-medium">
            </div>
            <button type="submit" class="w-full sm:w-auto h-11 px-6 bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm rounded-xl shadow transition-colors flex items-center justify-center gap-2 shrink-0">
                <x-lucide-search class="w-4 h-4" />
                <span>Search</span>
            </button>
        </form>

        @if(!empty($query))
            <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                <span>Showing search results for <strong class="text-slate-900">"{{ $query }}"</strong></span>
                <span class="bg-teal-500/10 text-teal-700 font-bold px-2.5 py-1 rounded-full border border-teal-500/20">{{ $totalMatches }} Matches</span>
            </div>
        @endif
    </div>

    @if($totalMatches > 0)
        <!-- ⚡ 1. Quick Actions & Command Shortcuts -->
        @if(count($matchedActions) > 0)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h2 class="font-bold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <x-lucide-zap class="w-4 h-4 text-amber-500" />
                    <span>Quick Command Actions</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($matchedActions as $action)
                        <a href="{{ $action['url'] }}" class="flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/80 hover:border-teal-500/40 bg-slate-50/50 hover:bg-slate-50 transition-all group">
                            <div class="w-9 h-9 rounded-xl bg-teal-500/10 text-teal-600 border border-teal-500/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                <x-dynamic-component :component="'lucide-' . $action['icon']" class="w-4 h-4" />
                            </div>
                            <div class="overflow-hidden">
                                <span class="font-bold text-slate-900 text-xs block group-hover:text-teal-700 transition-colors">{{ $action['title'] }}</span>
                                <span class="text-[11px] text-slate-500 truncate block">{{ $action['description'] }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 👥 2. Anglers & Crew -->
        @if($anglers->count() > 0)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h2 class="font-bold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <x-lucide-users class="w-4 h-4 text-teal-600" />
                    <span>Anglers & Crew ({{ $anglers->count() }})</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($anglers as $angler)
                        <a href="/angler/{{ $angler->id }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 hover:border-teal-500/40 bg-slate-50/50 hover:bg-slate-50 transition-all group">
                            <x-anglerAvatar :angler="$angler" size="sm" />
                            <div>
                                <span class="font-bold text-slate-900 text-xs block group-hover:text-teal-700 transition-colors">{{ $angler->fullName }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $angler->records_count }} Catches Logged</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 🌊 3. Lakes & Waters -->
        @if($lakes->count() > 0)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h2 class="font-bold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <x-lucide-waves class="w-4 h-4 text-teal-600" />
                    <span>Lakes & Waterbodies ({{ $lakes->count() }})</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($lakes as $lake)
                        <a href="/lake/{{ $lake->id }}" class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:border-teal-500/40 bg-slate-50/50 hover:bg-slate-50 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 border border-sky-500/20 flex items-center justify-center shrink-0">
                                    <x-lucide-map-pin class="w-4 h-4" />
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 text-xs block group-hover:text-teal-700 transition-colors">{{ $lake->name }}</span>
                                    <span class="text-[11px] text-slate-500 block">{{ $lake->structure ?? 'Waterbody' }} &bull; Max Depth: {{ $lake->max_depth ? $lake->max_depth . ' ft' : '—' }}</span>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold text-teal-700 bg-white px-2.5 py-1 rounded-lg border border-slate-200">{{ $lake->records_count }} Catches</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 🐟 4. Fish Species -->
        @if($fishBreeds->count() > 0)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h2 class="font-bold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <x-lucide-fish class="w-4 h-4 text-teal-600" />
                    <span>Fish Species ({{ $fishBreeds->count() }})</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($fishBreeds as $breed)
                        <a href="/fish/{{ $breed->id }}" class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:border-teal-500/40 bg-slate-50/50 hover:bg-slate-50 transition-all group">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 flex items-center justify-center shrink-0">
                                <x-lucide-fish class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 text-xs block group-hover:text-teal-700 transition-colors">{{ $breed->name }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $breed->family->name ?? 'Fish Family' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 🎣 5. Tacklebox -->
        @if($lures->count() > 0)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h2 class="font-bold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <x-lucide-fishing-hook class="w-4 h-4 text-teal-600" />
                    <span>Tacklebox ({{ $lures->count() }})</span>
                </h2>



                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($lures as $lure)
                        <a href="/lure/{{ $lure->id }}" class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200/80 hover:border-teal-500/40 bg-slate-50/50 hover:bg-slate-50 transition-all group">
                            <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 border border-indigo-500/20 flex items-center justify-center shrink-0">
                                <x-lucide-fishing-hook class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 text-xs block group-hover:text-teal-700 transition-colors">{{ $lure->name }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $lure->color }} &bull; {{ $lure->size }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 🏆 6. Catch Records -->
        @if($records->count() > 0)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                <h2 class="font-bold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <x-lucide-history class="w-4 h-4 text-teal-600" />
                    <span>Catches Logged ({{ $records->count() }})</span>
                </h2>

                <div class="divide-y divide-slate-100 border border-slate-200/80 rounded-xl overflow-hidden bg-white">
                    @foreach($records as $record)
                        <a href="/record/{{ $record->id }}" class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-700 flex items-center justify-center font-bold text-xs">
                                    <x-lucide-fish class="w-4 h-4" />
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 text-xs block group-hover:text-teal-700 transition-colors">
                                        {{ number_format($record->length, 2) }} in. {{ $record->fishBreed->name ?? 'Fish' }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 block">
                                        Logged by {{ $record->angler->fullName ?? 'Angler' }} on {{ $record->lake->name ?? 'Lake' }} ({{ $record->caught }})
                                    </span>
                                </div>
                            </div>
                            <x-lucide-chevron-right class="w-4 h-4 text-slate-400 group-hover:text-teal-600 transition-colors" />
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    @elseif(!empty($query))
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-slate-200/80 space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 border border-amber-500/20 flex items-center justify-center mx-auto">
                <x-lucide-search-x class="w-6 h-6" />
            </div>
            <h3 class="text-base font-bold text-slate-900">No Matches Found for "{{ $query }}"</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">Try searching for a different species name, lake location, crew member, or action command like "New Angler".</p>
            <a href="{{ route('search') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-colors">
                <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                <span>Reset Search</span>
            </a>
        </div>
    @endif
</div>
@endsection
