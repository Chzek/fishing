<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string|null $client_id
 * @property string|null $anglers_id
 * @property string|null $lakes_id
 * @property string|null $fish_breeds_id
 * @property string|null $lures_id
 * @property float|null $weight
 * @property float|null $length
 * @property float|null $temperature
 * @property float|null $latitude
 * @property float|null $longitude
 * @property bool $released
 * @property \Illuminate\Support\Carbon|null $caught
 * @property string|null $trip_id
 * @property int|null $month_num
 * @property float|null $max_length
 * @property int|null $catches
 * @property float|null $longest
 * @property int|null $count
 * @property int|null $total_catches
 * @property float|null $total_inches
 * @property float|null $avg_length
 * @property int|null $released_count
 * @property float|null $avg_water_temp
 * @property float|null $total_length
 * @property float|null $longest_fish
 * @property-read \Fishinglog\Models\Angler|null $angler
 * @property-read \Fishinglog\Models\Lake|null $lake
 * @property-read \Fishinglog\Models\FishBreed|null $fishBreed
 * @property-read \Fishinglog\Models\Lure|null $lure
 * @property-read \Fishinglog\Models\Expedition|null $expedition
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Photo> $photos
 */
class Record extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id', 'sync_status', 'synced_at', 'client_id', 'anglers_id', 'lakes_id', 'fish_breeds_id', 'lures_id',
        'weight', 'length', 'temperature', 'latitude', 'longitude', 'released',
        'caught', 'trip_id',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('angler_stats_overview');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('angler_stats_overview');
        });
    }

    public function angler(): BelongsTo
    {
        return $this->belongsTo(Angler::class, 'anglers_id', 'id');
    }

    public function lake(): BelongsTo
    {
        return $this->belongsTo(Lake::class, 'lakes_id', 'id');
    }

    public function fishBreed(): BelongsTo
    {
        return $this->belongsTo(FishBreed::class, 'fish_breeds_id', 'id');
    }

    public function lure(): BelongsTo
    {
        return $this->belongsTo(Lure::class, 'lures_id', 'id');
    }

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class, 'trip_id', 'id');
    }

    /**
     * Get all attached photos for this catch record.
     */
    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    /**
     * Get the primary display photo for this catch record.
     */
    public function primaryPhoto(): ?Photo
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

        $caughtDate = $this->caught instanceof \DateTimeInterface 
            ? $this->caught->format('Y-m-d') 
            : substr((string) $this->caught, 0, 10);

        if ($this->relationLoaded('lake') && $this->lake && $this->lake->relationLoaded('dailyWeather')) {
            return $this->lake->dailyWeather->first(function ($w) use ($caughtDate) {
                $wDate = $w->date instanceof \DateTimeInterface ? $w->date->format('Y-m-d') : substr((string) $w->date, 0, 10);
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
                'title' => "🎉 First Logged " . ($this->fishBreed ? $this->fishBreed->name : 'Species') . "!",
                'previous_length' => null,
                'previous_weight' => null,
            ];
        }

        $previousMax = (clone $previousCatches)->max('length');

        if ($this->length && $previousMax && (float) $this->length > (float) $previousMax) {
            return [
                'type' => 'species_pb',
                'title' => "🏆 New Personal Best " . ($this->fishBreed ? $this->fishBreed->name : 'Species') . "!",
                'previous_length' => round((float) $previousMax, 2),
                'previous_weight' => null,
            ];
        }

        return null;
    }
}
