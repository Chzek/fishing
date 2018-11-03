<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;

class Crew extends Model
{
    //
    public function expedition()
    {
      return $this->hasOne('\Fishinglog\Expedition');
    }

    public function angler()
    {
      return $this->hasOne('\Fishinglog\Angler', 'id', 'anglers_id');
    }

    public function records()
    {
      return $this->hasMany('\Fishinglog\Record', 'id', 'anglers_id');
    }
}
