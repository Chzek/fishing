<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expedition extends Model
{
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['id', 'sync_status', 'synced_at', 'description', 'title', 'start', 'finish'];

    public function crews()
    {
        return $this->hasMany(Crew::class, 'expeditions_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'expeditions_id', 'id');
    }

    public function records()
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
    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable')->orderBy('is_cover', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get the cover photo for this expedition trip.
     */
    public function coverPhoto(): ?Photo
    {
        if ($this->relationLoaded('photos')) {
            return $this->photos->firstWhere('is_cover', true) ?? $this->photos->first();
        }

        return $this->photos()->where('is_cover', true)->first() ?? $this->photos()->first();
    }
}
