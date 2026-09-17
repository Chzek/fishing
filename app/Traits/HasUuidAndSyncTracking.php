<?php

namespace Fishinglog\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

trait HasUuidAndSyncTracking
{
    use HasUuids;

    /**
     * Boot the trait for Eloquent models.
     */
    public static function bootHasUuidAndSyncTracking(): void
    {
        static::creating(function ($model) {
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

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
            static::deleted(function ($model) {
                if (!method_exists($model, 'isForceDeleting') || !$model->isForceDeleting()) {
                    $model->newQueryWithoutScopes()
                        ->where($model->getKeyName(), $model->getKey())
                        ->update(['sync_status' => 'pending_upstream']);
                    $model->sync_status = 'pending_upstream';
                }
            });

            static::registerModelEvent('restored', function ($model) {
                $model->newQueryWithoutScopes()
                    ->where($model->getKeyName(), $model->getKey())
                    ->update(['sync_status' => 'pending_upstream']);
                $model->sync_status = 'pending_upstream';
            });
        }
    }

    /**
     * Scope query to models pending upstream sync.
     */
    public function scopePendingUpstream($query)
    {
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
            return $query->withTrashed()->where('sync_status', 'pending_upstream');
        }

        return $query->where('sync_status', 'pending_upstream');
    }

    /**
     * Scope query to models already synced upstream.
     */
    public function scopeSynced($query)
    {
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
            return $query->withTrashed()->where('sync_status', 'synced');
        }

        return $query->where('sync_status', 'synced');
    }

    /**
     * Mark model as synced upstream.
     */
    public function markSynced(): bool
    {
        $this->setAttribute('sync_status', 'synced');
        $this->setAttribute('synced_at', now());

        return $this->saveQuietly();
    }
}
