<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lake extends Model
{
    //
    public function records()
    {
      return $this->hasMany('\App\Record', 'lakes_id', 'id');
    }
}
