<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishBreed extends Model
{
    use HasFactory;

    public function family()
    {
        return $this->belongsTo(FishFamily::class, 'fish_families_id', 'id');
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'fish_breeds_id', 'id');
    }
}
