<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Model;

class Expedition extends Model
{
    public function crews()
    {
        return $this->hasMany(Crew::class, 'expeditions_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'expeditions_id', 'id');
    }

    public function records()
    {
        return $this->hasManyThrough(
            Record::class,
            Crew::class,
            'expeditions_id',
            'anglers_id',
            'id',
            'anglers_id'
        );
    }
}
