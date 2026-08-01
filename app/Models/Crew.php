<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Model;

class Crew extends Model
{
    public function expedition()
    {
        return $this->belongsTo(Expedition::class, 'expeditions_id');
    }

    public function angler()
    {
        return $this->hasOne(Angler::class, 'id', 'anglers_id');
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'anglers_id', 'anglers_id');
    }
}
