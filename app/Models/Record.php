<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
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
     * Get daily weather matching record's lake and caught date.
     */
    public function getDailyWeatherAttribute()
    {
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
}
