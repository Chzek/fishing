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
    protected $signature = 'sync:nas {--url= : Override NAS URL} {--token= : Override NAS API Token}';

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
        $this->info('Starting Synology NAS Two-Way Synchronization...');

        $nasUrl = $this->option('url');
        $token = $this->option('token');

        $syncService = ($nasUrl || $token) 
            ? new NasSyncService($nasUrl, $token) 
            : $defaultSyncService;

        try {
            $pendingCount = $syncService->getPendingCount();
            $this->line("Found {$pendingCount} local record(s) pending upstream sync.");

            $result = $syncService->sync();

            $this->info('Synchronization finished successfully!');
            $this->table(
                ['Pushed Upstream', 'Pulled Downstream', 'Last Synced At'],
                [[$result['pushed'], $result['pulled'], $result['last_synced_at']]]
            );

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Sync failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
