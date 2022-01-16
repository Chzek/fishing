<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lake extends Model
{
  use HasFactory;
  
  //
  public function records()
  {
    return $this->hasMany('\Fishinglog\Record', 'lakes_id', 'id');
  }
}
