@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Hero Banner -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                <x-lucide-refresh-cw class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                    <span>Remote Synchronization & Diagnostic Console</span>
                    <span class="bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-semibold px-2.5 py-0.5 rounded-full font-mono">Admin Portal</span>
                </h1>
                <p class="text-xs text-slate-400 font-medium pt-0.5">
                    Real-time connection latency probes, TLS certificate health, 13-model outbox tracking, and chunked media pipeline diagnostics.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <x-lucide-arrow-left class="w-3.5 h-3.5 text-slate-400" />
                <span>Admin Dashboard</span>
            </a>
            <a href="{{ route('admin.users') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <x-lucide-users class="w-3.5 h-3.5 text-teal-400" />
                <span>User Linking</span>
            </a>
            <a href="{{ route('admin.trash') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors flex items-center gap-1.5">
                <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-400" />
                <span>Trash Bin</span>
            </a>
        </div>
    </div>

    <!-- Livewire Diagnostic Console Component -->
    <livewire:admin.sync-diagnostic-console />
</div>
@endsection
