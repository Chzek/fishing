<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string $photoable_type
 * @property string $photoable_id
 * @property string $path
 * @property string|null $original_name
 * @property string|null $caption
 * @property bool $is_cover
 * @property string|null $user_id
 * @property-read string $url
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $photoable
 * @property-read \Fishinglog\Models\User|null $user
 */
class Photo extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'photoable_type',
        'photoable_id',
        'path',
        'original_name',
        'caption',
        'is_cover',
        'user_id',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    /**
     * Get the owning photoable model (Record, Expedition, Angler, Lake, etc).
     */
    public function photoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded the photo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the publicly accessible URL for the photo.
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }

        return Storage::disk('public')->url($this->path);
    }
}
