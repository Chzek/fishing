<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string $expeditions_id
 * @property string $anglers_id
 * @property-read \Fishinglog\Models\Expedition|null $expedition
 * @property-read \Fishinglog\Models\Angler|null $angler
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Record> $records
 */
class Crew extends Model
{
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['id', 'sync_status', 'synced_at', 'expeditions_id', 'anglers_id'];

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class, 'expeditions_id');
    }

    public function angler(): HasOne
    {
        return $this->hasOne(Angler::class, 'id', 'anglers_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class, 'anglers_id', 'anglers_id');
    }
}
