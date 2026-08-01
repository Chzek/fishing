<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use HasFactory;
    use SoftDeletes;

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
}
