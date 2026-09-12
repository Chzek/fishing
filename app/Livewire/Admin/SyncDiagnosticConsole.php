<?php

namespace Fishinglog\Livewire\Admin;

use Fishinglog\Jobs\SyncNasJob;
use Fishinglog\Services\NasSyncService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SyncDiagnosticConsole extends Component
{
    public array $diagnostics = [];
    public array $modelMatrix = [];
    public array $mediaStatus = [];
    public string $targetName = '';
    public string $instanceName = '';
    public ?string $lastSyncedAt = null;
    public bool $filterPendingOnly = false;
    public bool $isTesting = false;
    public ?string $statusMessage = null;
    public string $statusType = 'success';

    public function mount(NasSyncService $syncService): void
    {
        $this->targetName = $syncService->getTargetName();
        $this->instanceName = $syncService->getInstanceName();
        $this->refreshData($syncService);
    }

    public function refreshData(NasSyncService $syncService): void
    {
        $this->diagnostics = $syncService->checkConnectivity();
        $this->modelMatrix = $syncService->getDetailedModelMatrix();
        $this->mediaStatus = $syncService->getMediaDiagnosticStatus();
        $this->lastSyncedAt = $syncService->getLastSyncedAt();
    }

    public function testConnection(NasSyncService $syncService): void
    {
        $this->isTesting = true;
        $this->diagnostics = $syncService->checkConnectivity(true);
        $this->isTesting = false;

        if ($this->diagnostics['online'] && $this->diagnostics['auth_valid']) {
            $this->statusMessage = "Connection to {$this->targetName} successful! Latency: {$this->diagnostics['latency_ms']}ms.";
            $this->statusType = 'success';
        } elseif ($this->diagnostics['online'] && !$this->diagnostics['auth_valid']) {
            $this->statusMessage = "Host is reachable, but API authorization failed ({$this->diagnostics['http_status']}). Verify NAS_API_TOKEN.";
            $this->statusType = 'error';
        } else {
            $this->statusMessage = "Failed to connect to {$this->targetName}: " . ($this->diagnostics['error_message'] ?? 'Connection timed out.');
            $this->statusType = 'error';
        }
    }

    public function triggerSync(): void
    {
        try {
            SyncNasJob::dispatch(false);
            $this->statusMessage = "Incremental {$this->targetName} synchronization job queued for background execution!";
            $this->statusType = 'success';
        } catch (\Throwable $e) {
            $this->statusMessage = "Failed to dispatch sync job: {$e->getMessage()}";
            $this->statusType = 'error';
        }
    }

    public function triggerBaselineSync(): void
    {
        try {
            SyncNasJob::dispatch(true);
            $this->statusMessage = "Full Baseline {$this->targetName} synchronization job queued for background execution!";
            $this->statusType = 'success';
        } catch (\Throwable $e) {
            $this->statusMessage = "Failed to dispatch baseline sync job: {$e->getMessage()}";
            $this->statusType = 'error';
        }
    }

    public function markAllSynced(NasSyncService $syncService): void
    {
        try {
            $count = $syncService->markAllSynced();
            $this->modelMatrix = $syncService->getDetailedModelMatrix();
            $this->statusMessage = "Successfully marked {$count} local record(s) across all 13 models as synced.";
            $this->statusType = 'success';
        } catch (\Throwable $e) {
            $this->statusMessage = "Failed to mark records synced: {$e->getMessage()}";
            $this->statusType = 'error';
        }
    }

    public function togglePendingFilter(): void
    {
        $this->filterPendingOnly = !$this->filterPendingOnly;
    }

    public function dismissMessage(): void
    {
        $this->statusMessage = null;
    }

    public function render(NasSyncService $syncService): View
    {
        // Compute summary metrics
        $totalRecords = collect($this->modelMatrix)->sum('total');
        $syncedRecords = collect($this->modelMatrix)->sum('synced');
        $pendingRecords = collect($this->modelMatrix)->sum('pending');
        $overallSyncPercent = $totalRecords > 0 ? (int) round(($syncedRecords / $totalRecords) * 100) : 100;

        $displayMatrix = $this->modelMatrix;
        if ($this->filterPendingOnly) {
            $displayMatrix = array_filter($displayMatrix, fn($item) => $item['pending'] > 0);
        }

        return view('livewire.admin.sync-diagnostic-console', [
            'displayMatrix' => $displayMatrix,
            'totalRecords' => $totalRecords,
            'syncedRecords' => $syncedRecords,
            'pendingRecords' => $pendingRecords,
            'overallSyncPercent' => $overallSyncPercent,
        ]);
    }
}
