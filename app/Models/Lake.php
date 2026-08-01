<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lake extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function records()
    {
        return $this->hasMany(Record::class, 'lakes_id', 'id');
    }

    public function anglers()
    {
        return $this->hasManyThrough(Angler::class, Record::class, 'lakes_id', 'id', 'id', 'anglers_id');
    }

    /**
     * Biggest fish by length of the lake.
     * 
     * @return \Fishinglog\Models\Record|null
     */
    public function biggestCatch()
    {
        return $this->records()
            ->orderBy('records.length', 'desc')
            ->first();
    }
}
