<?php

namespace Fishinglog\Jobs;

use Fishinglog\Services\NasSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncNasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(public bool $forceBaseline = false)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(NasSyncService $syncService): array
    {
        Log::info('Starting background NAS Sync Job', ['forceBaseline' => $this->forceBaseline]);

        try {
            $result = $syncService->sync($this->forceBaseline);
            Log::info('Background NAS Sync Job completed successfully', $result);
            return $result;
        } catch (\Throwable $e) {
            Log::error('Background NAS Sync Job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
