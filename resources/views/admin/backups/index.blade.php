@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Admin Hero Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-md border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/30 text-teal-400 flex items-center justify-center shrink-0">
                <x-lucide-hard-drive-download class="w-6 h-6" />
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">NAS & Spatie Backup Console</h1>
                <p class="text-xs text-slate-400">Monitor storage disk reachability, automated backup health, archive retention, and manual snapshots</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors">
                <x-lucide-shield-check class="w-4 h-4 text-slate-400" />
                <span>Overview</span>
            </a>
            <a href="{{ route('admin.sync') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors">
                <x-lucide-activity class="w-4 h-4 text-teal-400" />
                <span>Sync Console</span>
            </a>
            <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors">
                <x-lucide-users class="w-4 h-4 text-teal-400" />
                <span>User Linking</span>
            </a>
            <a href="{{ route('admin.trash') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-colors">
                <x-lucide-trash-2 class="w-4 h-4 text-rose-400" />
                <span>Trash Bin</span>
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    <x-statusAlert />

    <!-- Top Destination Telemetry & Actions Banner -->
    @foreach($destinations as $dest)
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6 transition-colors">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl {{ $dest['is_healthy'] ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' }} flex items-center justify-center shrink-0">
                        @if($dest['is_healthy'])
                            <x-lucide-shield-check class="w-5 h-5" />
                        @else
                            <x-lucide-alert-triangle class="w-5 h-5" />
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">{{ $dest['name'] }}</h2>
                            @if($dest['is_healthy'])
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-mono">
                                    ● Healthy & Reachable
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-mono">
                                    ▲ Unhealthy Check
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Storage Disk: <code class="font-mono font-bold text-slate-700 dark:text-slate-200">{{ $dest['disk'] }}</code>
                            @if($dest['failure_message'])
                                &bull; <span class="text-rose-600 dark:text-rose-400 font-semibold">{{ $dest['failure_message'] }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <form action="{{ route('admin.backups.create') }}" method="POST" class="inline-flex">
                        @csrf
                        <input type="hidden" name="only_db" value="1">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition-all cursor-pointer">
                            <x-lucide-database class="w-3.5 h-3.5" />
                            <span>Backup DB Now</span>
                        </button>
                    </form>

                    <form action="{{ route('admin.backups.create') }}" method="POST" class="inline-flex">
                        @csrf
                        <input type="hidden" name="only_db" value="0">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition-colors cursor-pointer" title="Includes database dump and storage/app/public assets">
                            <x-lucide-package class="w-3.5 h-3.5 text-teal-400" />
                            <span>Full Backup</span>
                        </button>
                    </form>

                    <form action="{{ route('admin.backups.clean') }}" method="POST" class="inline-flex" onsubmit="return confirm('Prune old backups according to retention policies?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer">
                            <x-lucide-brush class="w-3.5 h-3.5 text-amber-500" />
                            <span>Run Cleanup</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- KPI Metric Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Backups Count -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-bold uppercase tracking-wider text-[10px]">Total Archives</span>
                        <x-lucide-archive class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
                        {{ $dest['count'] }}
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Available snapshots on disk</p>
                </div>

                <!-- Storage Used Footprint -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-bold uppercase tracking-wider text-[10px]">Storage Used</span>
                        <x-lucide-hard-drive class="w-4 h-4 text-sky-600 dark:text-sky-400" />
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
                        {{ $dest['used_storage_formatted'] }}
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden mt-1">
                        @php
                            $maxBytes = $spatieConfig['max_megabytes'] * 1024 * 1024;
                            $pctUsed = $maxBytes > 0 ? min(100, round(($dest['used_storage_bytes'] / $maxBytes) * 100)) : 0;
                        @endphp
                        <div class="h-full bg-sky-500 rounded-full" style="width: {{ $pctUsed }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $pctUsed }}% of {{ number_format($spatieConfig['max_megabytes']) }} MB ceiling</p>
                </div>

                <!-- Latest Backup Run -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-bold uppercase tracking-wider text-[10px]">Latest Snapshot</span>
                        <x-lucide-clock class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="text-base font-bold text-slate-900 dark:text-white truncate">
                        {{ $dest['newest_backup'] ? $dest['newest_backup']->diffForHumans() : 'None logged' }}
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                        {{ $dest['newest_backup'] ? $dest['newest_backup']->format('M j, Y H:i') : '—' }}
                    </p>
                </div>

                <!-- Oldest Archive on Disk -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-bold uppercase tracking-wider text-[10px]">Oldest Retained</span>
                        <x-lucide-calendar class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="text-base font-bold text-slate-900 dark:text-white truncate">
                        {{ $dest['oldest_backup'] ? $dest['oldest_backup']->diffForHumans() : 'None logged' }}
                    </div>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                        {{ $dest['oldest_backup'] ? $dest['oldest_backup']->format('M j, Y H:i') : '—' }}
                    </p>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Spatie Backup Policy & Strategy Architecture Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <x-lucide-sliders class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                <span>Active Spatie Backup Configuration & Retention Policy</span>
            </h2>
            <span class="text-[11px] font-mono text-slate-400">config/backup.php</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Backup Sources</span>
                <div class="space-y-1 text-slate-700 dark:text-slate-300">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Databases:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ implode(', ', $spatieConfig['databases']) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Prefix:</span>
                        <span class="font-mono font-bold text-teal-600 dark:text-teal-400">{{ $spatieConfig['filename_prefix'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Compression:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">Zip Level {{ $spatieConfig['compression_level'] }}</span>
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Retention Pruning Timeline</span>
                <div class="space-y-1 text-slate-700 dark:text-slate-300">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Keep All Snapshots:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $spatieConfig['keep_all_days'] }} Days</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Daily Backups:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $spatieConfig['keep_daily_days'] }} Days</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Weekly Backups:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $spatieConfig['keep_weekly_weeks'] }} Weeks</span>
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Long-Term & Disk Quota</span>
                <div class="space-y-1 text-slate-700 dark:text-slate-300">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Monthly Backups:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $spatieConfig['keep_monthly_months'] }} Months</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Storage Cap:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ number_format($spatieConfig['max_megabytes']) }} MB</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Target Disks:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ implode(', ', $spatieConfig['disks']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Archives Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden transition-colors">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <x-lucide-file-archive class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                    <span>Backup Archives on Disk</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Chronological list of compressed snapshots stored on disk</p>
            </div>
            <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                {{ count($allBackups) }} Available
            </span>
        </div>

        @if(count($allBackups) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/40 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-mono">
                            <th class="py-3 px-4">Archive Filename</th>
                            <th class="py-3 px-4">Size</th>
                            <th class="py-3 px-4">Creation Timestamp</th>
                            <th class="py-3 px-4">Relative Age</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($allBackups as $backup)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20">
                                        <x-lucide-file-text class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <span class="block truncate max-w-md">{{ $backup['filename'] }}</span>
                                        <span class="text-[10px] text-slate-400 font-normal">{{ $backup['path'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-700 dark:text-slate-300">
                                    {{ $backup['size_formatted'] }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-400">
                                    {{ $backup['date_formatted'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 font-medium">
                                    {{ $backup['age_human'] }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.backups.download', ['disk' => $backup['disk'], 'file' => $backup['path']]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-teal-50 dark:bg-teal-950/60 hover:bg-teal-100 dark:hover:bg-teal-900/80 text-teal-700 dark:text-teal-300 rounded-lg border border-teal-200 dark:border-teal-800 font-semibold text-[11px] transition-colors" title="Download .zip Archive">
                                            <x-lucide-download class="w-3.5 h-3.5" />
                                            <span>Download</span>
                                        </a>

                                        <form action="{{ route('admin.backups.delete') }}" method="POST" class="inline-flex" onsubmit="return confirm('Delete backup file {{ $backup['filename'] }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="disk" value="{{ $backup['disk'] }}">
                                            <input type="hidden" name="file" value="{{ $backup['path'] }}">
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer" title="Delete Archive">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 px-4 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto">
                    <x-lucide-archive class="w-6 h-6" />
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No Backup Archives Found</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                    No snapshots were detected on the <code class="font-mono font-bold">backups</code> storage disk. Click "Backup DB Now" or "Full Backup" to generate your first archive.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
