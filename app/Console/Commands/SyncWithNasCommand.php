<?php

namespace Fishinglog\Console\Commands;

use Fishinglog\Services\NasSyncService;
use Illuminate\Console\Command;

class SyncWithNasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:nas {--url= : Override NAS URL} {--token= : Override NAS API Token} {--baseline : Force full baseline synchronization without timestamp filtering}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform two-way outbox sync between local laptop application and Synology NAS server';

    /**
     * Execute the console command.
     */
    public function handle(NasSyncService $defaultSyncService): int
    {
        $forceBaseline = (bool) $this->option('baseline');
        $this->info($forceBaseline ? 'Starting Full Baseline Synology NAS Synchronization...' : 'Starting Synology NAS Two-Way Synchronization...');

        $nasUrl = $this->option('url');
        $token = $this->option('token');

        $syncService = ($nasUrl || $token) 
            ? new NasSyncService($nasUrl, $token) 
            : $defaultSyncService;

        try {
            $pendingCount = $syncService->getPendingCount();
            $this->line("Found {$pendingCount} local record(s) pending upstream sync.");

            $result = $syncService->sync(forceBaseline: $forceBaseline);

            $this->info('Synchronization finished successfully!');
            $this->table(
                ['Pushed Upstream', 'Pulled Downstream', 'Last Synced At', 'Baseline Mode'],
                [[$result['pushed'], $result['pulled'], $result['last_synced_at'], $forceBaseline ? 'Yes' : 'No']]
            );

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Sync failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
