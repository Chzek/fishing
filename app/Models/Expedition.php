<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string|null $description
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $start
 * @property \Illuminate\Support\Carbon|null $finish
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Crew> $crews
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Post> $posts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Record> $records
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Photo> $photos
 */
class Expedition extends Model
{
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['id', 'sync_status', 'synced_at', 'description', 'title', 'start', 'finish'];

    public function crews(): HasMany
    {
        return $this->hasMany(Crew::class, 'expeditions_id', 'id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'expeditions_id', 'id');
    }

    public function records(): HasManyThrough
    {
        return $this->hasManyThrough(
            Record::class,
            Crew::class,
            'expeditions_id',
            'anglers_id',
            'id',
            'anglers_id'
        );
    }

    /**
     * Get all attached gallery photos for this expedition trip.
     */
    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable')->orderBy('is_cover', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get the cover photo for this expedition trip.
     */
    public function coverPhoto(): ?Photo
    {
        if ($this->relationLoaded('photos')) {
            /** @var Photo|null $photo */
            $photo = $this->photos->firstWhere('is_cover', true) ?? $this->photos->first();
            return $photo;
        }

        /** @var Photo|null $photo */
        $photo = $this->photos()->where('is_cover', true)->first() ?? $this->photos()->first();
        return $photo;
    }
}
