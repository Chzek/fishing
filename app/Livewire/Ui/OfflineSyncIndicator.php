<?php

namespace Fishinglog\Livewire\Ui;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OfflineSyncIndicator extends Component
{
    public bool $compact = false;

    public bool $showLabel = true;

    public ?string $lastSyncedAt = null;

    public int $syncedCount = 0;

    public function mount(bool $compact = false, bool $showLabel = true): void
    {
        $this->compact = $compact;
        $this->showLabel = $showLabel;
    }

    #[On('offline-sync-completed')]
    public function handleSyncCompleted(int $syncedCount = 0): void
    {
        if ($syncedCount > 0) {
            $this->syncedCount += $syncedCount;
            $this->lastSyncedAt = now()->toIso8601String();
        }
    }

    #[On('offline-queue-updated')]
    public function handleQueueUpdated(): void
    {
        // Livewire hook to trigger re-renders if necessary
    }

    public function render(): View
    {
        return view('livewire.ui.offline-sync-indicator');
    }
}
