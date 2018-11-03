<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;

class Expedition extends Model
{
    //
    public function crews()
    {
      return $this->hasMany('\Fishinglog\Crew', 'expeditions_id', 'id');
    }

    public function posts()
    {
      return $this->hasMany('\Fishinglog\Post', 'expeditions_id', 'id');
    }

    public function records()
    {
      return $this->hasManyThrough('Fishinglog\Record', 'Fishinglog\Crew', 'expeditions_id', 'anglers_id', 'id', 'anglers_id');
    }
}
