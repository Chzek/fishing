<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishingRule extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function fishingZone()
    {
        return $this->belongsTo(FishingZone::class, 'fishing_zone_id', 'id');
    }

    public function lake()
    {
        return $this->belongsTo(Lake::class, 'lake_id', 'id');
    }

    public function fishBreed()
    {
        return $this->belongsTo(FishBreed::class, 'fish_breed_id', 'id');
    }
}
