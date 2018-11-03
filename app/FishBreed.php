<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;

class FishBreed extends Model
{
    //
  public function family()
  {
    return $this->belongsTo('\Fishinglog\FishFamily', 'fish_families_id', 'id');
  }

  public function records()
    {
      return $this->hasMany('\Fishinglog\Record', 'fish_breeds_id', 'id');
    }
}
