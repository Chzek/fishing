@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Admin Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">System Administration Console</h1>
                <p class="text-xs text-slate-400">Manage registered user accounts, angler profile mappings, and soft-deleted trash recovery</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>User Account Linking</span>
            </a>
            <a href="{{ route('admin.trash') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4 text-rose-400"></i>
                <span>Trash Bin ({{ $trashedCount }})</span>
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold p-4 rounded-xl shadow-sm">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold p-4 rounded-xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Synchronization Engine Consoles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Synology NAS Sync Console -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-5 h-5 text-teal-400"></i>
                    <h2 class="text-base font-bold text-white">Synology NAS Two-Way Sync Engine</h2>
                </div>
                <p class="text-xs text-slate-300">
                    Synchronize local laptop catches, lakes, and anglers with your home Synology NAS server.
                </p>
                @php
                    $outboxSummary = !empty($pendingSyncBreakdown)
                        ? 'Pending push: ' . collect($pendingSyncBreakdown)->map(fn($item) => "{$item['count']} {$item['label']}")->join(', ')
                        : 'All models are synchronized with NAS';
                @endphp
                <div class="flex flex-wrap items-center gap-3 pt-1 text-xs font-medium text-slate-400 font-mono">
                    <div class="relative inline-block" x-data="{ showBreakdown: false }" @keydown.escape.window="showBreakdown = false" @click.outside="showBreakdown = false">
                        <button type="button"
                                @mouseenter="showBreakdown = true"
                                @mouseleave="showBreakdown = false"
                                @click="showBreakdown = !showBreakdown"
                                title="{{ $outboxSummary }}"
                                aria-label="Pending Outbox breakdown"
                                class="group inline-flex items-center gap-1.5 focus:outline-hidden text-left cursor-pointer">
                            <span class="text-slate-400">Pending Outbox:</span>
                            <strong class="text-amber-400 font-bold border-b border-dotted border-amber-400/60 group-hover:text-amber-300 group-hover:border-amber-300 transition-colors">{{ $pendingSyncCount }} item(s)</strong>
                            <i data-lucide="info" class="w-3.5 h-3.5 text-amber-400/80 group-hover:text-amber-300 transition-colors"></i>
                        </button>

                        <!-- Alpine Popover Card -->
                        <div x-show="showBreakdown"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             x-cloak
                             class="absolute z-30 left-0 bottom-full mb-2 w-64 bg-slate-900/95 backdrop-blur-md border border-slate-700/80 rounded-xl shadow-2xl p-3 text-xs text-slate-200">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2 mb-2">
                                <span class="font-bold text-slate-100 flex items-center gap-1.5">
                                    <i data-lucide="layers" class="w-3.5 h-3.5 text-teal-400"></i>
                                    Outbox by Model
                                </span>
                                <span class="text-[10px] font-mono bg-amber-500/20 text-amber-300 px-1.5 py-0.5 rounded font-bold">
                                    {{ $pendingSyncCount }} Total
                                </span>
                            </div>

                            @if(!empty($pendingSyncBreakdown))
                                <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                    @foreach($pendingSyncBreakdown as $item)
                                        <div class="flex items-center justify-between text-slate-300 font-mono text-[11px] bg-slate-800/40 hover:bg-slate-800/80 px-2 py-1 rounded transition-colors">
                                            <span class="flex items-center gap-1.5 text-slate-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                {{ $item['label'] }}
                                            </span>
                                            <span class="font-bold text-amber-400 bg-slate-800 px-1.5 py-0.5 rounded border border-slate-700">
                                                {{ $item['count'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-slate-400 text-center py-2 flex items-center justify-center gap-1.5">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-400"></i>
                                    <span>All models up to date</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <span>•</span>
                    <span>Last: <strong class="text-slate-200">{{ $lastSyncedAt ? \Illuminate\Support\Carbon::parse($lastSyncedAt)->diffForHumans() : 'Never' }}</strong></span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch gap-2 pt-1">
                <form action="{{ route('admin.sync.trigger') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg transition-all cursor-pointer">
                        <i data-lucide="cloud-sync" class="w-4 h-4"></i>
                        <span>Sync Now with NAS</span>
                    </button>
                </form>
                <form action="{{ route('admin.sync.baseline') }}" method="POST" onsubmit="return confirm('Perform a Full Baseline Pull from NAS? This will pull and reconcile all records regardless of timestamps.');">
                    @csrf
                    <button type="submit" title="Pull and reconcile all records from NAS from scratch" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-slate-800/90 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 font-bold text-xs rounded-xl transition-all cursor-pointer shadow-sm">
                        <i data-lucide="cloud-download" class="w-4 h-4 text-teal-400"></i>
                        <span class="whitespace-nowrap">Baseline Pull</span>
                    </button>
                </form>
                <form action="{{ route('admin.sync.mark_synced') }}" method="POST" onsubmit="return confirm('Mark all local records as synced? Use this if your records are already identical on NAS and you wish to clear pending outbox status.');">
                    @csrf
                    <button type="submit" title="Mark all local records as synced without pushing/pulling" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-slate-800/60 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700/80 font-semibold text-xs rounded-xl transition-all cursor-pointer shadow-sm">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
                        <span class="whitespace-nowrap">Mark All Synced</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Weather Telemetry Sync Console -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-sky-950 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="cloud-sun" class="w-5 h-5 text-sky-400"></i>
                        <h2 class="text-base font-bold text-white">Weather Telemetry Sync Engine</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-sky-300 bg-sky-950/80 px-2.5 py-0.5 rounded-full border border-sky-800">
                        {{ $weatherCoverageRate }}% Synced
                    </span>
                </div>
                <p class="text-xs text-slate-300">
                    Fetch atmospheric daily weather telemetry (air temp, pressure, wind) from Open-Meteo API.
                </p>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 pt-1 text-xs font-medium text-slate-400 font-mono">
                    <span>Pending Fetchable: <strong class="text-amber-400 font-bold">{{ number_format($pendingWeatherSyncCount) }} record(s)</strong></span>
                    <span>•</span>
                    <span>Synced: <strong class="text-emerald-400 font-bold">{{ number_format($weatherJoinedRecordsCount) }}</strong></span>
                    @if($missingCoordsRecordsCount > 0)
                        <span>•</span>
                        <span class="text-slate-400" title="Catch records on lakes missing latitude and longitude coordinates">Unmappable (No Lat/Lng): <strong class="text-rose-400 font-bold">{{ number_format($missingCoordsRecordsCount) }}</strong></span>
                    @endif
                </div>
            </div>

            <form action="{{ route('admin.weather.sync') }}" method="POST">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg transition-all shrink-0 cursor-pointer">
                    <i data-lucide="cloud-download" class="w-4 h-4"></i>
                    <span>Issue Weather Sync Now</span>
                </button>
            </form>
        </div>
    </div>

    <!-- System Telemetry Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Registered Users</span>
            <span class="text-2xl font-black text-slate-900 block">{{ number_format($users) }}</span>
            <a href="{{ route('admin.users') }}" class="text-[11px] font-bold text-teal-600 hover:underline inline-block pt-1">Manage Users →</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Anglers</span>
            <span class="text-2xl font-black text-slate-900 block">{{ number_format($anglers) }}</span>
            <a href="/angler" class="text-[11px] font-bold text-teal-600 hover:underline inline-block pt-1">View Profiles →</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Catches Logged</span>
            <span class="text-2xl font-black text-slate-900 block">{{ number_format($records) }}</span>
            <span class="text-[11px] font-semibold text-slate-500 block">{{ number_format($records / $years, 1) }} / year</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Lakes & Waters</span>
            <span class="text-2xl font-black text-slate-900 block">{{ number_format($lakes) }}</span>
            <a href="/lake" class="text-[11px] font-bold text-teal-600 hover:underline inline-block pt-1">Waterbody Index →</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Expeditions</span>
            <span class="text-2xl font-black text-slate-900 block">{{ number_format($expeditions) }}</span>
            <a href="/expedition" class="text-[11px] font-bold text-teal-600 hover:underline inline-block pt-1">Expedition Log →</a>
        </div>
    </div>

    <!-- Secondary Telemetry Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Tackle & Lures</span>
                <span class="text-xs font-mono font-bold text-slate-900">{{ $lures }} Total</span>
            </div>
            <p class="text-xs text-slate-500">Registered lures, baits, and terminal tackle entries.</p>
            <a href="/lure" class="inline-flex items-center gap-1 text-xs font-bold text-teal-600 hover:underline">Manage Lures →</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Fish Taxonomy</span>
                <span class="text-xs font-mono font-bold text-slate-900">{{ $fishBreeds }} Species / {{ $fishFamilies }} Families</span>
            </div>
            <p class="text-xs text-slate-500">Fish species, family taxonomy, and scientific breed metadata.</p>
            <a href="/fish" class="inline-flex items-center gap-1 text-xs font-bold text-teal-600 hover:underline">Taxonomy Directory →</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Trash Bin Recovery</span>
                <span class="text-xs font-mono font-bold text-rose-600">{{ $trashedCount }} Soft-Deleted</span>
            </div>
            <p class="text-xs text-slate-500">Restore accidentally deleted catches, lakes, or anglers.</p>
            <a href="{{ route('admin.trash') }}" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:underline">Open Trash Manager →</a>
        </div>
    </div>
</div>
@endsection
