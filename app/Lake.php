<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;

class Lake extends Model
{
    //
    public function records()
    {
      return $this->hasMany('\Fishinglog\Record', 'lakes_id', 'id');
    }
}
