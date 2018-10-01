<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expedition extends Model
{
    //
    public function crews()
    {
      return $this->hasMany('\App\Crew', 'expeditions_id', 'id');
    }

    public function posts()
    {
      return $this->hasMany('\App\Post', 'expeditions_id', 'id');
    }

    public function records()
    {
      return $this->hasManyThrough('App\Record', 'App\Crew', 'expeditions_id', 'anglers_id', 'id', 'anglers_id');
    }
}
