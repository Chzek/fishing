<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FishBreed extends Model
{
  use HasFactory;

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
