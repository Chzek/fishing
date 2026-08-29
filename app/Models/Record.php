<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'client_id',
        'anglers_id',
        'lakes_id',
        'fish_breeds_id',
        'lures_id',
        'weight',
        'length',
        'temperature',
        'latitude',
        'longitude',
        'released',
        'caught',
        'trip_id',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('angler_stats_overview');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('angler_stats_overview');
        });
    }

    public function angler()

    {
        return $this->belongsTo(Angler::class, 'anglers_id', 'id');
    }

    public function lake()
    {
        return $this->belongsTo(Lake::class, 'lakes_id', 'id');
    }

    public function fishBreed()
    {
        return $this->belongsTo(FishBreed::class, 'fish_breeds_id', 'id');
    }

    public function lure()
    {
        return $this->belongsTo(Lure::class, 'lures_id', 'id');
    }

    /**
     * Get all attached photos for this catch record.
     */
    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable')->orderBy('is_cover', 'desc')->orderBy('created_at', 'asc');
    }

    /**
     * Get the primary display photo for this catch record.
     */
    public function primaryPhoto(): ?Photo
    {
        if ($this->relationLoaded('photos')) {
            return $this->photos->firstWhere('is_cover', true) ?? $this->photos->first();
        }

        return $this->photos()->where('is_cover', true)->first() ?? $this->photos()->first();
    }

    /**
     * Get daily weather matching record's lake and caught date.
     */
    public function getDailyWeatherAttribute()
    {
        if ($this->relationLoaded('dailyWeather')) {
            return $this->getRelation('dailyWeather');
        }

        if (!$this->lakes_id || !$this->caught) {
            return null;
        }

        $caughtDate = is_a($this->caught, \DateTimeInterface::class) 
            ? $this->caught->format('Y-m-d') 
            : substr((string) $this->caught, 0, 10);

        if ($this->relationLoaded('lake') && $this->lake && $this->lake->relationLoaded('dailyWeather')) {
            return $this->lake->dailyWeather->first(function ($w) use ($caughtDate) {
                $wDate = is_a($w->date, \DateTimeInterface::class) ? $w->date->format('Y-m-d') : substr((string) $w->date, 0, 10);
                return $wDate === $caughtDate;
            });
        }

        return LakeDailyWeather::where('lakes_id', $this->lakes_id)
            ->where('date', $caughtDate)
            ->first();
    }

    /**
     * Determine if this catch record qualifies as a Personal Best or Trophy milestone.
     */
    public function checkTrophyMilestone(): ?array
    {
        if (!$this->anglers_id || !$this->fish_breeds_id || (empty($this->length) && empty($this->weight))) {
            return null;
        }

        $this->loadMissing(['fishBreed', 'lake']);

        // Check previous catches by this angler for this species (excluding current record)
        $previousCatches = self::where('anglers_id', $this->anglers_id)
            ->where('fish_breeds_id', $this->fish_breeds_id)
            ->where('id', '!=', $this->id);

        $previousCount = (clone $previousCatches)->count();
        if ($previousCount === 0) {
            return [
                'type' => 'first_species_catch',
                'title' => "🎉 First Logged " . ($this->fishBreed?->name ?? 'Species') . "!",
                'previous_length' => null,
                'previous_weight' => null,
            ];
        }

        $previousMax = (clone $previousCatches)->max('length');

        if ($this->length && $previousMax && (float) $this->length > (float) $previousMax) {
            return [
                'type' => 'species_pb',
                'title' => "🏆 New Personal Best " . ($this->fishBreed?->name ?? 'Species') . "!",
                'previous_length' => round((float) $previousMax, 2),
                'previous_weight' => null,
            ];
        }

        return null;
    }
}

