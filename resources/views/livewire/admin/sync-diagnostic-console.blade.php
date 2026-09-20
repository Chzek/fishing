<div class="space-y-6" wire:poll.15s="refreshData">
    <!-- Header Notification Message Banner -->
    @if($statusMessage)
        <div class="rounded-2xl p-4 shadow-md border flex items-center justify-between gap-3 transition-all animate-fadeIn {{ $statusType === 'success' ? 'bg-emerald-950/80 border-emerald-500/40 text-emerald-200' : ($statusType === 'error' ? 'bg-rose-950/80 border-rose-500/40 text-rose-200' : 'bg-slate-900 border-slate-700 text-slate-200') }}">
            <div class="flex items-center gap-3">
                @if($statusType === 'success')
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                        <x-lucide-check-circle-2 class="w-5 h-5" />
                    </div>
                @elseif($statusType === 'error')
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0">
                        <x-lucide-alert-triangle class="w-5 h-5" />
                    </div>
                @else
                    <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center shrink-0">
                        <x-lucide-info class="w-5 h-5" />
                    </div>
                @endif
                <div>
                    <p class="text-xs font-semibold">{{ $statusMessage }}</p>
                </div>
            </div>
            <button type="button" wire:click="dismissMessage" class="text-xs opacity-70 hover:opacity-100 px-2 py-1 rounded-lg bg-black/20 hover:bg-black/40 transition-colors">
                Dismiss
            </button>
        </div>
    @endif

    <!-- Diagnostic Telemetry Grid (4 Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Connectivity & Latency -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-2xl p-5 shadow-lg border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center">
                            <x-lucide-activity class="w-4 h-4" />
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">Ping & Latency</h3>
                    </div>
                    @if($diagnostics['online'] ?? false)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            ONLINE
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono bg-rose-500/20 text-rose-400 border border-rose-500/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                            OFFLINE
                        </span>
                    @endif
                </div>

                <div class="pt-1">
                    <div class="flex items-baseline gap-2">
                        @if(!is_null($diagnostics['latency_ms'] ?? null))
                            <span class="text-3xl font-extrabold font-mono {{ $diagnostics['latency_ms'] < 60 ? 'text-emerald-400' : ($diagnostics['latency_ms'] < 200 ? 'text-amber-400' : 'text-rose-400') }}">
                                {{ $diagnostics['latency_ms'] }}
                            </span>
                            <span class="text-xs font-mono text-slate-400">ms round-trip</span>
                        @else
                            <span class="text-2xl font-bold font-mono text-slate-500">Unreachable</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1 truncate" title="{{ $diagnostics['target_url'] ?? 'Not set' }}">
                        Target: <span class="font-mono text-slate-300">{{ $diagnostics['target_url'] ?? 'Not Configured' }}</span>
                    </p>
                </div>
            </div>

            <button type="button"
                    wire:click="testConnection"
                    wire:loading.attr="disabled"
                    class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-slate-800 hover:bg-slate-700 disabled:opacity-50 text-slate-200 hover:text-white text-xs font-bold rounded-xl border border-slate-700 transition-all cursor-pointer shadow-sm">
                <x-lucide-radio class="w-3.5 h-3.5 text-teal-400" wire:loading.remove wire:target="testConnection" />
                <x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin text-teal-400" wire:loading wire:target="testConnection" />
                <span wire:loading.remove wire:target="testConnection">Run Live Ping Probe</span>
                <span wire:loading wire:target="testConnection">Probing {{ $targetName }}...</span>
            </button>
        </div>

        <!-- 2. TLS / SSL Certificate Inspector -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-2xl p-5 shadow-lg border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center">
                            <x-lucide-lock class="w-4 h-4" />
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">TLS / SSL Certificate</h3>
                    </div>
                    @if($diagnostics['ssl_valid'] ?? false)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                            VALID
                        </span>
                    @elseif($diagnostics['ssl_enabled'] ?? false)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-amber-500/20 text-amber-400 border border-amber-500/40">
                            TLS ACTIVE
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-slate-800 text-slate-400 border border-slate-700">
                            HTTP (NO SSL)
                        </span>
                    @endif
                </div>

                <div class="pt-1 space-y-1">
                    <div class="text-xs font-bold text-slate-200 truncate" title="{{ $diagnostics['ssl_issuer'] ?? 'N/A' }}">
                        {{ $diagnostics['ssl_issuer'] ?? 'Standard TLS' }}
                    </div>
                    <div class="text-[11px] text-slate-400 flex items-center gap-1.5 font-mono">
                        <x-lucide-calendar class="w-3 h-3 text-slate-500" />
                        @if(!empty($diagnostics['ssl_expires_at']))
                            <span>Expires: <strong class="text-slate-200">{{ \Illuminate\Support\Carbon::parse($diagnostics['ssl_expires_at'])->format('M d, Y') }}</strong></span>
                        @else
                            <span>Encrypted Channel Active</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pt-1 border-t border-slate-800/80 flex items-center justify-between text-[11px] font-mono text-slate-400">
                <span>Protocol:</span>
                <span class="text-cyan-400 font-bold">{{ ($diagnostics['ssl_enabled'] ?? false) ? 'HTTPS / TLS 1.3' : 'HTTP Plain' }}</span>
            </div>
        </div>

        <!-- 3. API Authorization & Endpoints -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-2xl p-5 shadow-lg border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center">
                            <x-lucide-key-round class="w-4 h-4" />
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">API Authorization</h3>
                    </div>
                    @if($diagnostics['auth_valid'] ?? false)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                            200 AUTHORIZED
                        </span>
                    @elseif(($diagnostics['http_status'] ?? 0) === 401 || ($diagnostics['http_status'] ?? 0) === 403)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-rose-500/20 text-rose-400 border border-rose-500/40">
                            UNAUTHORIZED
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-slate-800 text-slate-400 border border-slate-700">
                            HTTP {{ $diagnostics['http_status'] ?? '—' }}
                        </span>
                    @endif
                </div>

                <div class="pt-1 space-y-1 text-[11px] text-slate-400 font-mono">
                    <div class="flex justify-between">
                        <span>Instance:</span>
                        <strong class="text-slate-200">{{ $instanceName }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Target:</span>
                        <strong class="text-purple-300">{{ $targetName }}</strong>
                    </div>
                </div>
            </div>

            <div class="pt-1 border-t border-slate-800/80 flex items-center justify-between text-[11px] font-mono text-slate-400">
                <span>Auth Scheme:</span>
                <span class="text-purple-400 font-bold">Bearer Token</span>
            </div>
        </div>

        <!-- 4. Binary Media Assets Pipeline -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-2xl p-5 shadow-lg border border-slate-800 flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center">
                            <x-lucide-hard-drive class="w-4 h-4" />
                        </div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">Media Pipeline</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-amber-500/20 text-amber-400 border border-amber-500/40">
                        {{ $mediaStatus['hashing_algorithm'] ?? 'SHA-256' }}
                    </span>
                </div>

                <div class="pt-1">
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold font-mono text-amber-400">
                            {{ $mediaStatus['total_media_records'] ?? 0 }}
                        </span>
                        <span class="text-xs font-mono text-slate-400">tracked assets</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        {{ $mediaStatus['photos_with_file'] ?? 0 }} photos • {{ $mediaStatus['anglers_avatars_count'] ?? 0 }} avatars • {{ $mediaStatus['species_artwork_count'] ?? 0 }} fish
                    </p>
                </div>
            </div>

            <div class="pt-1 border-t border-slate-800/80 flex items-center justify-between text-[11px] font-mono text-slate-400">
                <span>Chunk Size:</span>
                <span class="text-amber-400 font-bold">{{ $mediaStatus['chunk_size_mb'] ?? '1.0' }} MB Slice</span>
            </div>
        </div>
    </div>

    <!-- Main Sync Controls & Quick Action Bar -->
    <div class="bg-slate-900 rounded-2xl p-5 shadow-md border border-slate-800 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div class="space-y-1">
            <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                <x-lucide-git-compare class="w-4 h-4 text-teal-400" />
                <span>Synchronization Management & Operations</span>
            </h2>
            <p class="text-xs text-slate-400">
                Execute incremental push/pull, full baseline pull from remote {{ $targetName }}, or clear dirty tracking flags.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
            <!-- Sync Now Button -->
            <button type="button"
                    wire:click="triggerSync"
                    wire:loading.attr="disabled"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-teal-500 hover:bg-teal-400 disabled:opacity-50 text-slate-950 font-bold text-xs rounded-xl shadow-lg transition-all cursor-pointer">
                <x-lucide-cloud-sync class="w-4 h-4" wire:loading.remove wire:target="triggerSync" />
                <x-lucide-loader-2 class="w-4 h-4 animate-spin" wire:loading wire:target="triggerSync" />
                <span>Sync Now</span>
            </button>

            <!-- Baseline Pull Button -->
            <button type="button"
                    wire:click="triggerBaselineSync"
                    wire:confirm="Perform a Full Baseline Pull from {{ $targetName }}? This will pull and reconcile all records regardless of timestamps."
                    wire:loading.attr="disabled"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 disabled:opacity-50 text-slate-200 hover:text-white border border-slate-700 font-bold text-xs rounded-xl transition-all cursor-pointer shadow-sm">
                <x-lucide-cloud-download class="w-3.5 h-3.5 text-teal-400" wire:loading.remove wire:target="triggerBaselineSync" />
                <x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin text-teal-400" wire:loading wire:target="triggerBaselineSync" />
                <span>Baseline Pull</span>
            </button>

            <!-- Mark All Synced Button -->
            <button type="button"
                    wire:click="markAllSynced"
                    wire:confirm="Mark all local records as synced? Use this if your records are already identical on {{ $targetName }} and you wish to clear pending outbox status."
                    wire:loading.attr="disabled"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-slate-800/80 hover:bg-slate-700 disabled:opacity-50 text-slate-300 hover:text-white border border-slate-700/80 font-semibold text-xs rounded-xl transition-all cursor-pointer shadow-sm">
                <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-400" wire:loading.remove wire:target="markAllSynced" />
                <x-lucide-loader-2 class="w-3.5 h-3.5 animate-spin text-emerald-400" wire:loading wire:target="markAllSynced" />
                <span>Mark Synced</span>
            </button>
        </div>
    </div>

    <!-- 13-Model Sync & Outbox Matrix Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-md overflow-hidden">
        <!-- Matrix Header -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                        <x-lucide-database class="w-4 h-4" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight">13-Model Outbox & Sync Health Matrix</h2>
                    <span class="px-2 py-0.5 text-[10px] font-bold font-mono rounded-full bg-slate-200/80 text-slate-700">
                        {{ count($displayMatrix) }} / 13 Models
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    Database records tracked with UUID primary keys and timestamped synchronization states.
                </p>
            </div>

            <!-- Global Database Sync Progress & Filter Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Overall Sync Progress Pill -->
                <div class="bg-white px-3.5 py-1.5 rounded-xl border border-slate-200/80 shadow-xs flex items-center gap-3">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">Overall Sync</span>
                        <span class="text-xs font-extrabold font-mono text-slate-800">{{ $overallSyncPercent }}%</span>
                    </div>
                    <div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-teal-500 h-2 rounded-full transition-all duration-500" style="width: {{ $overallSyncPercent }}%"></div>
                    </div>
                </div>

                <!-- Filter Pending Toggle -->
                <button type="button"
                        wire:click="togglePendingFilter"
                        class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 {{ $filterPendingOnly ? 'bg-amber-500 text-slate-950 border-amber-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200/80 hover:bg-slate-50' }}">
                    <x-lucide-filter class="w-3.5 h-3.5" />
                    <span>{{ $filterPendingOnly ? 'Showing Pending Only' : 'Show Pending Only' }}</span>
                    @if($pendingRecords > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ $filterPendingOnly ? 'bg-slate-950 text-amber-300' : 'bg-amber-100 text-amber-800' }}">
                            {{ $pendingRecords }}
                        </span>
                    @endif
                </button>

                <!-- Refresh Button -->
                <button type="button"
                        wire:click="refreshData"
                        class="p-2 rounded-xl bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:bg-slate-50 shadow-xs transition-colors cursor-pointer"
                        title="Refresh Matrix">
                    <x-lucide-refresh-cw class="w-3.5 h-3.5" wire:loading.class="animate-spin" wire:target="refreshData" />
                </button>
            </div>
        </div>

        <!-- Table View of the 13 Models -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-950/70 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 font-mono transition-colors">
                        <th class="py-3 px-5">Entity / Model</th>
                        <th class="py-3 px-4">Sync Progress</th>
                        <th class="py-3 px-4 text-center">Synced</th>
                        <th class="py-3 px-4 text-center">Pending Push</th>
                        <th class="py-3 px-4">Local Activity</th>
                        <th class="py-3 px-4">Last Synced</th>
                        <th class="py-3 px-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70 text-xs text-slate-700 dark:text-slate-300 font-sans transition-colors">
                    @forelse($displayMatrix as $item)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50 transition-colors {{ $item['pending'] > 0 ? 'bg-amber-50/30 dark:bg-amber-950/20' : '' }}">
                            <!-- Model Label & Key -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold {{ $item['pending'] > 0 ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300' : 'bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300' }}">
                                        @if($item['key'] === 'records')
                                            <x-lucide-fish class="w-4 h-4" />
                                        @elseif($item['key'] === 'photos')
                                            <x-lucide-image class="w-4 h-4" />
                                        @elseif($item['key'] === 'lakes')
                                            <x-lucide-waves class="w-4 h-4" />
                                        @elseif($item['key'] === 'expeditions')
                                            <x-lucide-ship class="w-4 h-4" />
                                        @elseif($item['key'] === 'anglers')
                                            <x-lucide-user-check class="w-4 h-4" />
                                        @elseif($item['key'] === 'lures')
                                            <x-lucide-anchor class="w-4 h-4" />
                                        @elseif($item['key'] === 'fish_breeds')
                                            <x-lucide-tag class="w-4 h-4" />
                                        @elseif($item['key'] === 'posts')
                                            <x-lucide-file-text class="w-4 h-4" />
                                        @elseif($item['key'] === 'users')
                                            <x-lucide-users class="w-4 h-4" />
                                        @else
                                            <x-lucide-layers class="w-4 h-4" />
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-slate-100">{{ $item['label'] }}</div>
                                        <div class="text-[10px] font-mono text-slate-400 dark:text-slate-500">{{ $item['key'] }} ({{ number_format($item['total']) }})</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Progress Bar -->
                            <td class="py-3.5 px-4 min-w-[140px]">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-[10px] font-mono font-bold">
                                        <span class="{{ $item['percent'] === 100 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $item['percent'] }}%</span>
                                        <span class="text-slate-400 dark:text-slate-500">{{ number_format($item['synced']) }}/{{ number_format($item['total']) }}</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all duration-300 {{ $item['percent'] === 100 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $item['percent'] }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Synced Count -->
                            <td class="py-3.5 px-4 text-center font-mono font-semibold text-slate-800 dark:text-slate-200">
                                {{ number_format($item['synced']) }}
                            </td>

                            <!-- Pending Count -->
                            <td class="py-3.5 px-4 text-center">
                                @if($item['pending'] > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold font-mono bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800/60">
                                        {{ number_format($item['pending']) }} pending
                                    </span>
                                @else
                                    <span class="font-mono text-slate-400 dark:text-slate-500 text-[11px]">0</span>
                                @endif
                            </td>

                            <!-- Local Activity Timestamp -->
                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $item['latest_local'] }}
                            </td>

                            <!-- Synced Timestamp -->
                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $item['latest_synced'] }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-5 text-right">
                                @if($item['pending'] === 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                        <x-lucide-check class="w-3 h-3" />
                                        SYNCED
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                        <x-lucide-clock class="w-3 h-3" />
                                        OUTBOX
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400 dark:text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-lucide-check-circle-2 class="w-8 h-8 text-emerald-500" />
                                    <p class="font-medium text-slate-600 dark:text-slate-400 text-xs">No pending sync records found across all models.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer / Legend -->
        <div class="p-4 bg-slate-50/60 dark:bg-slate-950/70 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-500 dark:text-slate-400 transition-colors">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Synchronized with remote</span>
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Pending upstream push</span>
                </span>
            </div>
            <div class="font-mono text-[10px] text-slate-400 dark:text-slate-500">
                Last Synchronized: <strong class="text-slate-600 dark:text-slate-300">{{ $lastSyncedAt ? \Illuminate\Support\Carbon::parse($lastSyncedAt)->diffForHumans() : 'Never' }}</strong>
            </div>
        </div>
    </div>
</div>
