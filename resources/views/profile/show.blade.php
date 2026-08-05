@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl p-4 flex items-center gap-3 shadow-sm" role="alert">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (isset($angler))
        <!-- Hero Angler Profile Header -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center gap-5 text-center md:text-left">
                <div class="relative">
                    <img src="/storage/avatars/{{ ($angler->avatar ?: 'user.jpg') }}" alt="{{ $angler->firstName }} {{ $angler->lastName }}" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-teal-500/10 shadow-md">
                    <div class="absolute -bottom-1 -right-1 bg-teal-500 text-white rounded-full p-1 shadow">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $angler->firstName }} {{ $angler->lastName }}</h1>
                    <p class="text-xs font-medium text-slate-500 mt-0.5 flex items-center justify-center md:justify-start gap-1.5">
                        <i data-lucide="anchor" class="w-3.5 h-3.5 text-teal-600"></i>
                        <span>Registered Angler Logbook</span>
                    </p>
                    @if($angler->bio)
                        <p class="text-sm text-slate-600 mt-2 max-w-xl italic">"{{ $angler->bio }}"</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ url('/record/quick') }}" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-500 text-white text-sm font-semibold py-2.5 px-4 rounded-xl shadow-md shadow-teal-900/10 transition-all">
                    <i data-lucide="zap" class="w-4 h-4"></i>
                    <span>Quick Catch</span>
                </a>
            </div>
        </div>

        <!-- Metrics Key Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Lakes Visited -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Lakes Visited</span>
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1 block">{{ $lake_count }}</span>
                    <span class="text-[11px] text-teal-600 font-medium mt-1 inline-flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3 h-3"></i> Unique Waters
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
                    <i data-lucide="waves" class="w-6 h-6"></i>
                </div>
            </div>

            <!-- Fish Caught -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Fish Caught</span>
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1 block">{{ $record_count }}</span>
                    <span class="text-[11px] text-emerald-600 font-medium mt-1 inline-flex items-center gap-1">
                        <i data-lucide="trending-up" class="w-3 h-3"></i> Logbook Catches
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <i data-lucide="fish" class="w-6 h-6"></i>
                </div>
            </div>

            <!-- Expeditions -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Expeditions</span>
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1 block">{{ $crews }}</span>
                    <span class="text-[11px] text-sky-600 font-medium mt-1 inline-flex items-center gap-1">
                        <i data-lucide="navigation" class="w-3 h-3"></i> Crew Trips
                    </span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <i data-lucide="ship" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Personal Best Trophies Cards Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i data-lucide="trophy" class="w-5 h-5 text-amber-500"></i>
                    <span>Personal Best Trophies</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Trophy By Length -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-md border border-slate-700/50 relative overflow-hidden">
                    <div class="absolute -right-3 -bottom-3 text-amber-500/10 pointer-events-none">
                        <i data-lucide="ruler" class="w-24 h-24"></i>
                    </div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-400 bg-amber-400/10 px-2.5 py-1 rounded-md border border-amber-400/20">Longest Catch</span>
                        <i data-lucide="award" class="w-5 h-5 text-amber-400"></i>
                    </div>
                    @if(isset($personalBest['byLength']) && $personalBest['byLength'])
                        <div class="mt-2">
                            <span class="text-3xl font-black text-white tracking-tight">{{ $personalBest['byLength']->length }} <span class="text-base font-medium text-slate-300">in.</span></span>
                            <span class="block text-sm font-semibold text-teal-300 mt-1">{{ $personalBest['byLength']->fishBreed->name ?? 'Fish' }}</span>
                            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3 text-slate-500 shrink-0"></i>
                                <span class="truncate">{{ $personalBest['byLength']->lake->name ?? 'Lake' }}</span>
                            </p>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $personalBest['byLength']->caught }}</p>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-400 text-xs italic">
                            No length record yet. Ready to catch a monster?
                        </div>
                    @endif
                </div>

                <!-- Trophy By Weight -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-md border border-slate-700/50 relative overflow-hidden">
                    <div class="absolute -right-3 -bottom-3 text-amber-500/10 pointer-events-none">
                        <i data-lucide="scale" class="w-24 h-24"></i>
                    </div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-400 bg-amber-400/10 px-2.5 py-1 rounded-md border border-amber-400/20">Heaviest Catch</span>
                        <i data-lucide="award" class="w-5 h-5 text-amber-400"></i>
                    </div>
                    @if(isset($personalBest['byWeight']) && $personalBest['byWeight'])
                        <div class="mt-2">
                            <span class="text-3xl font-black text-white tracking-tight">{{ $personalBest['byWeight']->weight }} <span class="text-base font-medium text-slate-300">lbs.</span></span>
                            <span class="block text-sm font-semibold text-teal-300 mt-1">{{ $personalBest['byWeight']->fishBreed->name ?? 'Fish' }}</span>
                            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3 text-slate-500 shrink-0"></i>
                                <span class="truncate">{{ $personalBest['byWeight']->lake->name ?? 'Lake' }}</span>
                            </p>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $personalBest['byWeight']->caught }}</p>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-400 text-xs italic">
                            No weight record logged yet. Weigh your next catch!
                        </div>
                    @endif
                </div>

                <!-- Trophy Top Hotspot Lake -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-5 shadow-md border border-slate-700/50 relative overflow-hidden">
                    <div class="absolute -right-3 -bottom-3 text-teal-500/10 pointer-events-none">
                        <i data-lucide="compass" class="w-24 h-24"></i>
                    </div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-teal-400 bg-teal-400/10 px-2.5 py-1 rounded-md border border-teal-400/20">Top Hotspot</span>
                        <i data-lucide="flame" class="w-5 h-5 text-amber-400"></i>
                    </div>
                    @if(isset($personalBest['lakeWithMostCatches']) && $personalBest['lakeWithMostCatches'])
                        <div class="mt-2">
                            <span class="text-xl font-bold text-white tracking-tight block truncate">{{ $personalBest['lakeWithMostCatches']->name }}</span>
                            <span class="text-xs text-teal-300 block mt-1">Most Successful Angling Water</span>
                            <div class="mt-4 pt-2 border-t border-slate-700/60 flex items-center gap-1 text-xs text-slate-400">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-teal-400"></i>
                                <span>High catch probability location</span>
                            </div>
                        </div>
                    @else
                        <div class="py-4 text-center text-slate-400 text-xs italic">
                            Log catches to reveal your top hotspot!
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Catches Logbook Data Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden space-y-4">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <i data-lucide="list" class="w-5 h-5 text-teal-600"></i>
                    <span>Recent Logged Catches</span>
                </h2>
                <a href="{{ url('/record') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1">
                    <span>View All Logbook</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            @if(count($records) > 0)
                <div class="p-5 pt-0 space-y-6">
                    @foreach($records as $dateKey => $catchGroup)
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200/60 w-fit">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-teal-600"></i>
                                <span>{{ $dateKey }}</span>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-slate-200/60">
                                <table class="w-full text-left text-sm text-slate-700">
                                    <thead class="bg-slate-50/80 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200/60">
                                        <tr>
                                            <th scope="col" class="py-3 px-4">Lake / Water</th>
                                            <th scope="col" class="py-3 px-4">Fish Species</th>
                                            <th scope="col" class="py-3 px-4 text-center">Weight (lbs)</th>
                                            <th scope="col" class="py-3 px-4 text-center">Length (in)</th>
                                            <th scope="col" class="py-3 px-4 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @foreach($catchGroup as $catch)
                                            <tr class="hover:bg-slate-50/60 transition-colors">
                                                <td class="py-3 px-4 font-medium text-slate-900 flex items-center gap-2">
                                                    <i data-lucide="waves" class="w-4 h-4 text-teal-600 shrink-0"></i>
                                                    <span>{{ $catch->lake->name ?? 'Unknown Lake' }}</span>
                                                </td>
                                                <td class="py-3 px-4 font-semibold text-slate-800">
                                                    {{ $catch->fishBreed->name ?? 'Species' }}
                                                </td>
                                                <td class="py-3 px-4 text-center font-mono font-medium">
                                                    {{ $catch->weight ? number_format($catch->weight, 2) : '—' }}
                                                </td>
                                                <td class="py-3 px-4 text-center font-mono font-medium">
                                                    {{ $catch->length ? number_format($catch->length, 2) : '—' }}
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    @if($catch->released == 1)
                                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full border border-emerald-200">
                                                            <i data-lucide="heart" class="w-3 h-3 text-emerald-500"></i> Released
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 bg-sky-50 text-sky-700 text-xs font-bold px-2.5 py-1 rounded-full border border-sky-200">
                                                            <i data-lucide="shopping-bag" class="w-3 h-3 text-sky-500"></i> Kept
                                                        </span>
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
                <div class="p-8 text-center text-slate-500 text-sm space-y-3">
                    <i data-lucide="fish-off" class="w-10 h-10 text-slate-300 mx-auto"></i>
                    <p>No catches logged yet for this angler.</p>
                    <a href="{{ url('/record/quick') }}" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-500 text-white font-medium text-xs px-4 py-2 rounded-xl transition-colors">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Log First Catch Now</span>
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl p-6 text-center space-y-3 shadow-sm">
            <i data-lucide="alert-triangle" class="w-8 h-8 text-amber-500 mx-auto"></i>
            <h3 class="font-bold text-base">User Not Associated with Angler Record</h3>
            <p class="text-xs text-amber-700 max-w-md mx-auto">Your account is not linked to an Angler profile. Please contact your system administrator to associate your account.</p>
        </div>
    @endif
</div>
@endsection
