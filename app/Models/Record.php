<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

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
 * @property Point|null $location
 * @property bool $released
 * @property \Illuminate\Support\Carbon|null $caught
 * @property string|null $caught_date
 * @property string|null $trip_id
 * @property int|null $month_num
 * @property float|null $max_length
 * @property int|null $catches
 * @property int|null $catches_count
 * @property float|null $longest
 * @property float|null $longest_catch
 * @property float|null $heaviest_catch
 * @property int|null $count
 * @property int|null $total_catches
 * @property float|null $total_inches
 * @property float|null $avg_length
 * @property int|null $released_count
 * @property float|null $avg_water_temp
 * @property float|null $total_length
 * @property float|null $longest_fish
 * @property-read float $trophy_score
 * @property-read array{key: string, label: string, badge_variant: string, icon: string, is_trophy: bool} $trophy_tier
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
    use HasSpatial;
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id', 'sync_status', 'synced_at', 'client_id', 'anglers_id', 'lakes_id', 'fish_breeds_id', 'lures_id',
        'weight', 'length', 'temperature', 'latitude', 'longitude', 'location', 'released',
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'location' => Point::class,
            'released' => 'boolean',
            'caught' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::saving(function (Record $record) {
            if ($record->isDirty(['latitude', 'longitude'])) {
                if ($record->latitude && $record->longitude && (float) $record->latitude != 0 && (float) $record->longitude != 0) {
                    $record->location = new Point((float) $record->latitude, (float) $record->longitude, 4326);
                } else {
                    $record->location = null;
                }
            } elseif ($record->isDirty('location') && $record->location instanceof Point) {
                $record->latitude = $record->location->latitude;
                $record->longitude = $record->location->longitude;
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('angler_stats_overview');
            \Fishinglog\Services\CatchTelemetryService::clearCache();
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('angler_stats_overview');
            \Fishinglog\Services\CatchTelemetryService::clearCache();
        });

        static::restored(function () {
            \Illuminate\Support\Facades\Cache::forget('angler_stats_overview');
            \Fishinglog\Services\CatchTelemetryService::clearCache();
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
     * Scope a query to eager load lake daily weather efficiently for collections.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDailyWeather($query)
    {
        return $query->with(['lake.dailyWeather']);
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
     * Hierarchy:
     * 1. all_time_record: Longest ever across the entire logbook for this species
     * 2. lake_record: Longest ever on this specific waterbody for this species
     * 3. species_pb: Longest ever for this angler for this species
     * 4. first_species_catch: First catch of this species logged by this angler
     *
     * @return array<string, mixed>|null
     */
    public function checkTrophyMilestone(): ?array
    {
        if (!$this->anglers_id || !$this->fish_breeds_id || (empty($this->length) && empty($this->weight))) {
            return null;
        }

        $this->loadMissing(['fishBreed', 'lake', 'angler']);
        $speciesName = $this->fishBreed ? $this->fishBreed->name : 'Fish';
        $lakeName = $this->lake ? $this->lake->name : 'Waterbody';

        // 1. Check Logbook-Wide All-Time Record for this species
        $allTimeCatches = self::where('fish_breeds_id', $this->fish_breeds_id)
            ->where('id', '!=', $this->id);
        $allTimeCount = (clone $allTimeCatches)->count();
        $allTimeMax = (clone $allTimeCatches)->max('length');

        if ($allTimeCount > 0 && $this->length && $allTimeMax && (float) $this->length > (float) $allTimeMax) {
            return [
                'type' => 'all_time_record',
                'title' => "🌟 All-Time Logbook Record {$speciesName}!",
                'badge_label' => "🌟 All-Time Record",
                'previous_length' => round((float) $allTimeMax, 2),
                'previous_weight' => null,
                'species_name' => $speciesName,
                'lake_name' => $lakeName,
            ];
        }

        // 2. Check Lake Record for this species
        if ($this->lakes_id) {
            $lakeCatches = self::where('lakes_id', $this->lakes_id)
                ->where('fish_breeds_id', $this->fish_breeds_id)
                ->where('id', '!=', $this->id);
            $lakeCount = (clone $lakeCatches)->count();
            $lakeMax = (clone $lakeCatches)->max('length');

            if ($lakeCount > 0 && $this->length && $lakeMax && (float) $this->length > (float) $lakeMax) {
                return [
                    'type' => 'lake_record',
                    'title' => "👑 New {$lakeName} Record {$speciesName}!",
                    'badge_label' => "👑 {$lakeName} Record",
                    'previous_length' => round((float) $lakeMax, 2),
                    'previous_weight' => null,
                    'species_name' => $speciesName,
                    'lake_name' => $lakeName,
                ];
            }
        }

        // 3. Check Angler Personal Best for this species
        $anglerCatches = self::where('anglers_id', $this->anglers_id)
            ->where('fish_breeds_id', $this->fish_breeds_id)
            ->where('id', '!=', $this->id);
        $anglerCount = (clone $anglerCatches)->count();
        $anglerMax = (clone $anglerCatches)->max('length');

        if ($anglerCount === 0) {
            return [
                'type' => 'first_species_catch',
                'title' => "🎉 First Logged {$speciesName}!",
                'badge_label' => "🎉 First Catch",
                'previous_length' => null,
                'previous_weight' => null,
                'species_name' => $speciesName,
                'lake_name' => $lakeName,
            ];
        }

        if ($this->length && $anglerMax && (float) $this->length > (float) $anglerMax) {
            return [
                'type' => 'species_pb',
                'title' => "🏆 New Personal Best {$speciesName}!",
                'badge_label' => "🏆 Personal Best",
                'previous_length' => round((float) $anglerMax, 2),
                'previous_weight' => null,
                'species_name' => $speciesName,
                'lake_name' => $lakeName,
            ];
        }

        return null;
    }

    /**
     * Get the species-normalized trophy score (percentage of Master Angler benchmark).
     */
    public function getTrophyScoreAttribute(): float
    {
        return app(\Fishinglog\Services\TrophyScoringService::class)->calculateScore($this);
    }

    /**
     * Get trophy tier metadata for this catch.
     *
     * @return array{key: string, label: string, badge_variant: string, icon: string, is_trophy: bool}
     */
    public function getTrophyTierAttribute(): array
    {
        return app(\Fishinglog\Services\TrophyScoringService::class)->getTrophyTier($this->trophy_score);
    }
}
