<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\FishBreed> $breeds
 */
class FishFamily extends Model
{
    use HasFactory;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['id', 'sync_status', 'synced_at', 'name'];

    public function breeds(): HasMany
    {
        return $this->hasMany(FishBreed::class, 'fish_families_id', 'id');
    }
}
