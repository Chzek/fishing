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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start' => 'datetime',
            'finish' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

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

    /**
     * Get all distinct angler IDs associated with this expedition (from registered crew + catch logs).
     *
     * @return \Illuminate\Support\Collection<int, int|string>
     */
    public function getAnglerIdsAttribute(): \Illuminate\Support\Collection
    {
        $rosterIds = $this->crews()->pluck('anglers_id')->filter();
        $catchingIds = ($this->start && $this->finish)
            ? Record::where('caught', '>=', $this->start)
                ->where('caught', '<=', $this->finish)
                ->whereNotNull('anglers_id')
                ->pluck('anglers_id')
            : collect();

        return $rosterIds->concat($catchingIds)->unique()->values();
    }

    /**
     * Get all distinct anglers associated with this expedition (from registered crew + catch logs).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Angler>
     */
    public function getAnglersAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        $ids = $this->angler_ids;
        if ($ids->isEmpty()) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        return Angler::whereIn('id', $ids)->get();
    }

    /**
     * Get count of distinct anglers associated with this expedition.
     */
    public function getAnglersCountAttribute(): int
    {
        if (array_key_exists('anglers_count', $this->attributes)) {
            return (int) $this->attributes['anglers_count'];
        }

        return $this->angler_ids->count();
    }
}
