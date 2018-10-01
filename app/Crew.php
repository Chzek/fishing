<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Crew extends Model
{
    //
    public function expedition()
    {
      return $this->hasOne('\App\Expedition');
    }

    public function angler()
    {
      return $this->hasOne('\App\Angler', 'id', 'anglers_id');
    }

    public function records()
    {
      return $this->hasMany('\App\Record', 'id', 'anglers_id');
    }
}
