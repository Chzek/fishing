<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string|null $fishing_zone_id
 * @property string|null $lake_id
 * @property string|null $fish_breed_id
 * @property string|null $species_name
 * @property bool $is_aggregate
 * @property string|null $aggregate_group
 * @property string|null $season
 * @property string|null $sport_limit
 * @property string|null $conservation_limit
 * @property string|null $size_restriction
 * @property string|null $notes
 * @property-read \Fishinglog\Models\FishingZone|null $fishingZone
 * @property-read \Fishinglog\Models\Lake|null $lake
 * @property-read \Fishinglog\Models\FishBreed|null $fishBreed
 */
class FishingRule extends Model
{
    use HasFactory;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'fishing_zone_id',
        'lake_id',
        'fish_breed_id',
        'species_name',
        'is_aggregate',
        'aggregate_group',
        'season',
        'sport_limit',
        'conservation_limit',
        'size_restriction',
        'notes',
    ];

    public function fishingZone(): BelongsTo
    {
        return $this->belongsTo(FishingZone::class, 'fishing_zone_id', 'id');
    }

    public function lake(): BelongsTo
    {
        return $this->belongsTo(Lake::class, 'lake_id', 'id');
    }

    public function fishBreed(): BelongsTo
    {
        return $this->belongsTo(FishBreed::class, 'fish_breed_id', 'id');
    }
}
