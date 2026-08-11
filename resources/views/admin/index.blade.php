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
                <div class="flex items-center gap-3 pt-1 text-xs font-medium text-slate-400 font-mono">
                    <span>Pending Outbox: <strong class="text-amber-400 font-bold">{{ $pendingSyncCount }} item(s)</strong></span>
                    <span>•</span>
                    <span>Last: <strong class="text-slate-200">{{ $lastSyncedAt ? \Illuminate\Support\Carbon::parse($lastSyncedAt)->diffForHumans() : 'Never' }}</strong></span>
                </div>
            </div>

            <form action="{{ route('admin.sync.trigger') }}" method="POST">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg transition-all shrink-0 cursor-pointer">
                    <i data-lucide="cloud-sync" class="w-4 h-4"></i>
                    <span>Sync Now with NAS</span>
                </button>
            </form>
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
                <div class="flex items-center gap-3 pt-1 text-xs font-medium text-slate-400 font-mono">
                    <span>Pending Weather Sync: <strong class="text-amber-400 font-bold">{{ number_format($pendingWeatherSyncCount) }} record(s)</strong></span>
                    <span>•</span>
                    <span>Synced: <strong class="text-emerald-400 font-bold">{{ number_format($weatherJoinedRecordsCount) }}</strong></span>
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
