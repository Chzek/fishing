@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Admin Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <x-lucide-shield-check class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">System Administration Console</h1>
                <p class="text-xs text-slate-400">Manage registered user accounts, angler profile mappings, and soft-deleted trash recovery</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors">
                <x-lucide-users class="w-4 h-4" />
                <span>User Account Linking</span>
            </a>
            <a href="{{ route('admin.trash') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors">
                <x-lucide-trash-2 class="w-4 h-4 text-rose-400" />
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

    <!-- Unread User Registration Notifications & Unlinked Accounts Banner -->
    @if(!empty($unreadNotifications) && $unreadNotifications->count() > 0)
        <div class="bg-amber-50/80 rounded-2xl p-5 border border-amber-200/80 shadow-sm space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-600 flex items-center justify-center shrink-0">
                        <x-lucide-bell class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 tracking-tight flex items-center gap-2">
                            <span>New User Registration Alert</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-200/70 text-amber-900 border border-amber-300 font-mono">{{ $unreadNotifications->count() }} New</span>
                        </h2>
                        <p class="text-xs text-slate-600 mt-0.5">
                            New user accounts have registered and are waiting to be paired with an Angler profile.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.users') }}" class="px-3.5 py-2 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center gap-1.5 cursor-pointer">
                        <x-lucide-user-check class="w-3.5 h-3.5" />
                        <span>Pair in User Accounts →</span>
                    </a>
                    <form action="{{ route('admin.notifications.mark_read') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200/80 shadow-sm transition-colors cursor-pointer">
                            Dismiss All
                        </button>
                    </form>
                </div>
            </div>

            <!-- List of Unread Notifications -->
            <div class="divide-y divide-amber-200/60 border-t border-amber-200/60 pt-2 space-y-2">
                @foreach($unreadNotifications as $notification)
                    <div class="flex items-center justify-between text-xs text-slate-700 pt-2">
                        <div class="flex items-center gap-2">
                            <x-lucide-user-plus class="w-3.5 h-3.5 text-teal-600 shrink-0" />
                            <span class="font-medium text-slate-800">{{ $notification->data['message'] ?? 'New user registered.' }}</span>
                            <span class="text-[10px] text-slate-500 font-mono">({{ $notification->created_at->diffForHumans() }})</span>
                        </div>
                        <form action="{{ route('admin.notifications.mark_single_read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[11px] text-slate-500 hover:text-slate-800 hover:underline font-medium">
                                Dismiss
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(!empty($unlinkedUsersCount) && $unlinkedUsersCount > 0)
        <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-4 text-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <x-lucide-user-x class="w-4 h-4" />
                </div>
                <div>
                    <h3 class="text-xs font-bold text-white">Unlinked Registered Accounts ({{ $unlinkedUsersCount }})</h3>
                    <p class="text-[11px] text-amber-300/80">There are {{ $unlinkedUsersCount }} registered user account(s) not linked to an Angler profile.</p>
                </div>
            </div>
            <a href="{{ route('admin.users') }}" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow transition-colors">
                Manage Linkings →
            </a>
        </div>
    @endif

    <!-- Synchronization Engine Consoles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Two-Way Sync Console -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <x-lucide-refresh-cw class="w-5 h-5 text-teal-400" />
                    <h2 class="text-base font-bold text-white">{{ $syncTargetName === 'Laptop' ? 'Field Laptop Two-Way Sync Engine' : 'Synology NAS Two-Way Sync Engine' }}</h2>
                </div>
                <p class="text-xs text-slate-300">
                    @if($syncTargetName === 'Laptop')
                        Synchronize server catches, lakes, and anglers with your field laptop.
                    @else
                        Synchronize local laptop catches, lakes, and anglers with your home Synology NAS server.
                    @endif
                </p>
                @php
                    $outboxSummary = !empty($pendingSyncBreakdown)
                        ? 'Pending push: ' . collect($pendingSyncBreakdown)->map(fn($item) => "{$item['count']} {$item['label']}")->join(', ')
                        : "All models are synchronized with {$syncTargetName}";
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
                            <x-lucide-info class="w-3.5 h-3.5 text-amber-400/80 group-hover:text-amber-300 transition-colors" />
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
                                    <x-lucide-layers class="w-3.5 h-3.5 text-teal-400" />
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
                                    <x-lucide-check-circle class="w-3.5 h-3.5 text-emerald-400" />
                                    <span>All models up to date</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <span>•</span>
                    <span>Last: <strong class="text-slate-200">{{ $lastSyncedAt ? \Illuminate\Support\Carbon::parse($lastSyncedAt)->diffForHumans() : 'Never' }}</strong></span>
                </div>
            </div>

            <div class="flex flex-col gap-2 pt-1">
                <form action="{{ route('admin.sync.trigger') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg transition-all cursor-pointer">
                        <x-lucide-cloud-sync class="w-4 h-4" />
                        <span>Sync Now with {{ $syncTargetName }}</span>
                    </button>
                </form>
                <div class="grid grid-cols-2 gap-2">
                    <form action="{{ route('admin.sync.baseline') }}" method="POST" onsubmit="return confirm('Perform a Full Baseline Pull from {{ $syncTargetName }}? This will pull and reconcile all records regardless of timestamps.');">
                        @csrf
                        <button type="submit" title="Pull and reconcile all records from {{ $syncTargetName }} from scratch" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-800/90 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 font-bold text-[11px] rounded-xl transition-all cursor-pointer shadow-sm">
                            <x-lucide-cloud-download class="w-3.5 h-3.5 text-teal-400" />
                            <span>Baseline Pull</span>
                        </button>
                    </form>
                    <form action="{{ route('admin.sync.mark_synced') }}" method="POST" onsubmit="return confirm('Mark all local records as synced? Use this if your records are already identical on {{ $syncTargetName }} and you wish to clear pending outbox status.');">
                        @csrf
                        <button type="submit" title="Mark all local records as synced without pushing/pulling" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-800/60 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700/80 font-semibold text-[11px] rounded-xl transition-all cursor-pointer shadow-sm">
                            <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-400" />
                            <span>Mark Synced</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Weather Telemetry Sync Console -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-sky-950 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-lucide-cloud-sun class="w-5 h-5 text-sky-400" />
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
                    <x-lucide-cloud-download class="w-4 h-4" />
                    <span>Issue Weather Sync Now</span>
                </button>
            </form>
        </div>

        <!-- Laravel Pulse System Telemetry Card -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-purple-950 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-lucide-activity class="w-5 h-5 text-purple-400" />
                        <h2 class="text-base font-bold text-white">Laravel Pulse Telemetry</h2>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 font-mono">
                        Admin Only
                    </span>
                </div>
                <p class="text-xs text-slate-300">
                    Real-time server performance telemetry, slow queries, job queues, outdated composer packages, and system resource monitors.
                </p>
                <div class="pt-1 flex items-center gap-2 text-xs text-purple-300/80 font-mono">
                    <x-lucide-shield-check class="w-3.5 h-3.5 text-purple-400" />
                    <span>Restricted to Authorized Administrators</span>
                </div>
            </div>

            <a href="{{ url('/pulse') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-lg transition-all shrink-0 cursor-pointer">
                <x-lucide-bar-chart-3 class="w-4 h-4" />
                <span>Open Pulse Dashboard →</span>
            </a>
        </div>
    </div>

    <!-- System Telemetry Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <x-kpiMetric label="Registered Users" :value="$users" icon="users" color="teal" actionUrl="{{ route('admin.users') }}" actionLabel="Manage Users →" />
        <x-kpiMetric label="Anglers" :value="$anglers" icon="user-check" color="emerald" actionUrl="/angler" actionLabel="View Profiles →" />
        <x-kpiMetric label="Catches Logged" :value="$records" icon="fish" color="sky" subtext="{{ number_format($records / $years, 1) }} / yr" />
        <x-kpiMetric label="Lakes & Waters" :value="$lakes" icon="waves" color="amber" actionUrl="/lake" actionLabel="Waterbody Index →" />
        <x-kpiMetric label="Expeditions" :value="$expeditions" icon="ship" color="purple" actionUrl="/expedition" actionLabel="Expedition Log →" />
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
