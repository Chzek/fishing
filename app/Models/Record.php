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

    public function dailyWeather()
    {
        return $this->hasOne(LakeDailyWeather::class, 'lakes_id', 'lakes_id')
            ->whereColumn('lake_daily_weather.date', 'records.caught');
    }
}
