<div x-data="offlineSyncIndicator()" x-init="init()" class="relative inline-flex items-center">
    <!-- Trigger Pill / Button -->
    <button type="button" 
            @click="toggleFlyout()" 
            class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-teal-500/50"
            :class="{
                'bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500/30 shadow-sm shadow-amber-950/30': pendingCount > 0 && !isSyncing,
                'bg-teal-500/20 text-teal-300 border border-teal-500/40 hover:bg-teal-500/30 animate-pulse': isSyncing,
                'bg-slate-800/80 text-amber-400 border border-amber-500/30 hover:bg-slate-800': !isOnline && pendingCount === 0,
                'bg-slate-800/60 text-slate-300 border border-slate-700/60 hover:bg-slate-800 hover:text-white': isOnline && pendingCount === 0 && !isSyncing
            }"
            :title="getTooltip()">
        
        <!-- Status Indicator Icon -->
        <template x-if="isSyncing">
            <x-lucide-refresh-cw class="w-3.5 h-3.5 animate-spin text-teal-400 shrink-0" />
        </template>
        <template x-if="!isSyncing && pendingCount > 0">
            <span class="relative flex h-2 w-2 shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
            </span>
        </template>
        <template x-if="!isSyncing && pendingCount === 0 && isOnline">
            <span class="inline-flex rounded-full h-2 w-2 bg-emerald-400 shrink-0 shadow-sm shadow-emerald-500/50"></span>
        </template>
        <template x-if="!isSyncing && pendingCount === 0 && !isOnline">
            <x-lucide-wifi-off class="w-3.5 h-3.5 text-amber-400 shrink-0" />
        </template>

        <!-- Status Label -->
        @if($showLabel)
            <span class="font-medium tracking-wide">
                <template x-if="isSyncing">
                    <span>Syncing...</span>
                </template>
                <template x-if="!isSyncing && pendingCount > 0">
                    <span><strong x-text="pendingCount"></strong> Queued</span>
                </template>
                <template x-if="!isSyncing && pendingCount === 0 && isOnline">
                    <span class="text-slate-400 group-hover:text-slate-300">Online</span>
                </template>
                <template x-if="!isSyncing && pendingCount === 0 && !isOnline">
                    <span>Boat Mode</span>
                </template>
            </span>
        @else
            <template x-if="pendingCount > 0">
                <span class="px-1.5 py-0.2 bg-amber-400 text-slate-950 font-black text-[10px] rounded-full" x-text="pendingCount"></span>
            </template>
        @endif
    </button>

    <!-- Inspection Flyout Popover -->
    <div x-show="flyoutOpen" 
         @click.outside="flyoutOpen = false"
         @keydown.escape.window="flyoutOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
         class="absolute right-0 top-full mt-2 w-80 sm:w-96 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden text-slate-200"
         style="display: none;">
        
        <!-- Flyout Header -->
        <div class="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-950/60">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 flex items-center justify-center">
                    <x-lucide-database class="w-4 h-4" />
                </div>
                <div>
                    <h4 class="font-bold text-sm text-white flex items-center gap-2">
                        <span>Offline Catch Queue</span>
                    </h4>
                    <p class="text-[11px] text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full" :class="isOnline ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                        <span x-text="isOnline ? 'Connected to Network' : 'Offline / Remote Boat Mode'"></span>
                    </p>
                </div>
            </div>
            
            <button @click="flyoutOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        <!-- Pending Items List -->
        <div class="p-4 max-h-64 overflow-y-auto space-y-2.5 divide-y divide-slate-800/60">
            <template x-if="pendingCatches.length === 0">
                <div class="text-center py-6 space-y-2">
                    <div class="w-10 h-10 rounded-full bg-slate-800/80 text-emerald-400 flex items-center justify-center mx-auto">
                        <x-lucide-check-circle-2 class="w-5 h-5" />
                    </div>
                    <p class="text-xs font-semibold text-slate-300">All Catches Synchronized</p>
                    <p class="text-[11px] text-slate-500">No pending boat logs in IndexedDB queue.</p>
                </div>
            </template>

            <template x-for="(item, index) in pendingCatches" :key="item.client_id || index">
                <div class="pt-2 first:pt-0 flex items-center justify-between gap-3 group">
                    <div class="space-y-0.5 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white" x-text="item.length ? item.length + ' in. Catch' : 'Catch Record'"></span>
                            <span x-show="item.weight" class="text-[10px] text-teal-400 font-medium" x-text="'(' + item.weight + ' lbs)'"></span>
                        </div>
                        <p class="text-[11px] text-slate-400 truncate" x-text="item.caught || 'Queued Recently'"></p>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" 
                                @click="removeCatch(item.client_id)" 
                                title="Remove from queue"
                                class="p-1.5 text-slate-500 hover:text-red-400 hover:bg-slate-800 rounded-lg transition-colors">
                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Flyout Actions Footer -->
        <div class="p-3 bg-slate-950/80 border-t border-slate-800 flex items-center justify-between gap-2">
            <a href="{{ url('/record/offline-review') }}" class="text-xs font-semibold text-teal-400 hover:text-teal-300 flex items-center gap-1 transition-colors">
                <span>Detailed Inspection</span>
                <x-lucide-chevron-right class="w-3.5 h-3.5" />
            </a>

            <div class="flex items-center gap-2">
                <template x-if="pendingCatches.length > 0">
                    <button type="button" 
                            @click="triggerSync()" 
                            :disabled="!isOnline || isSyncing"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 hover:bg-teal-500 disabled:bg-slate-800 disabled:text-slate-600 text-white font-bold text-xs rounded-xl shadow transition-all cursor-pointer">
                        <span :class="isSyncing ? 'animate-spin' : ''" class="inline-flex">
                            <x-lucide-refresh-cw class="w-3 h-3" />
                        </span>
                        <span x-text="isSyncing ? 'Syncing...' : 'Sync Now'"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function offlineSyncIndicator() {
    return {
        isOnline: navigator.onLine,
        isSyncing: false,
        pendingCount: 0,
        pendingCatches: [],
        flyoutOpen: false,
        lastSyncTime: null,
        errorMessage: null,

        init() {
            this.refreshQueue();

            window.addEventListener('online', () => {
                this.isOnline = true;
                this.refreshQueue();
            });

            window.addEventListener('offline', () => {
                this.isOnline = false;
                this.refreshQueue();
            });

            window.addEventListener('offline-queue-updated', (e) => {
                this.pendingCount = e.detail?.count || 0;
                this.pendingCatches = e.detail?.pending || [];
            });

            window.addEventListener('offline-sync-started', () => {
                this.isSyncing = true;
            });

            window.addEventListener('offline-sync-completed', (e) => {
                this.isSyncing = false;
                this.lastSyncTime = new Date().toLocaleTimeString();
                this.refreshQueue();
            });

            window.addEventListener('offline-sync-error', (e) => {
                this.isSyncing = false;
                this.errorMessage = e.detail?.message;
            });
        },

        async refreshQueue() {
            if (window.offlineSyncManager) {
                try {
                    const pending = await window.offlineSyncManager.getPendingCatches();
                    this.pendingCatches = pending || [];
                    this.pendingCount = this.pendingCatches.length;
                } catch (e) {
                    console.error('Failed to read offline catches:', e);
                }
            }
        },

        toggleFlyout() {
            this.flyoutOpen = !this.flyoutOpen;
            if (this.flyoutOpen) {
                this.refreshQueue();
            }
        },

        async triggerSync() {
            if (!this.isOnline) {
                alert('Device is offline. Connect to Wi-Fi or cellular network to sync catches.');
                return;
            }
            if (window.offlineSyncManager) {
                await window.offlineSyncManager.syncNow();
            }
        },

        async removeCatch(clientId) {
            if (!clientId) return;
            if (confirm('Remove this catch from the offline boat queue?')) {
                if (window.offlineSyncManager) {
                    await window.offlineSyncManager.removePendingCatch(clientId);
                    await this.refreshQueue();
                }
            }
        },

        getTooltip() {
            if (this.isSyncing) return 'Synchronizing pending catches...';
            if (this.pendingCount > 0) return `${this.pendingCount} catch(es) pending in boat offline queue`;
            if (!this.isOnline) return 'Offline (Boat Mode) - Catches will be stored locally';
            return 'Connected - All catches synchronized';
        }
    };
}
</script>
