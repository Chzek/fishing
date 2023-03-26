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

  public function anglers()
  {
    return $this->hasManyThrough('\Fishinglog\Angler', '\Fishinglog\Record', 'lakes_id', 'id', 'id', 'anglers_id');
  }

  /**
   * Biggest fish by length of the lake.
   * 
   * @return \Fishinglog\Record
   */
  public function biggestCatch()
  {
    return $this->records()
      ->orderBy('records.length', 'desc')
      ->first();
  }
}
