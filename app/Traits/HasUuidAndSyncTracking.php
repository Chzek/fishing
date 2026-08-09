<?php

namespace Fishinglog\Traits;

use Illuminate\Support\Str;

trait HasUuidAndSyncTracking
{
    /**
     * Boot the trait for Eloquent models.
     */
    public static function bootHasUuidAndSyncTracking(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

            if (empty($model->sync_status)) {
                $model->sync_status = 'pending_upstream';
            }
        });

        static::updating(function ($model) {
            // Only update sync_status if it wasn't explicitly modified (e.g., during sync ingestion)
            if (!$model->isDirty('sync_status')) {
                $model->sync_status = 'pending_upstream';
            }
        });
    }

    /**
     * Scope query to models pending upstream sync.
     */
    public function scopePendingUpstream($query)
    {
        return $query->where('sync_status', 'pending_upstream');
    }

    /**
     * Scope query to models already synced upstream.
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Mark model as synced upstream.
     */
    public function markSynced(): bool
    {
        $this->sync_status = 'synced';
        $this->synced_at = now();

        return $this->saveQuietly();
    }
}
