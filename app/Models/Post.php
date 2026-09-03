<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string $expeditions_id
 * @property string $anglers_id
 * @property string|null $date
 * @property string|null $description
 * @property string $title
 * @property string|null $body
 * @property-read \Fishinglog\Models\Expedition|null $expedition
 * @property-read \Fishinglog\Models\Angler|null $creator
 */
class Post extends Model
{
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['id', 'sync_status', 'synced_at', 'expeditions_id', 'anglers_id', 'date', 'description', 'title', 'body'];

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class, 'expeditions_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Angler::class, 'anglers_id');
    }
}
