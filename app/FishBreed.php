<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FishBreed extends Model
{
    //
  public function family()
  {
    return $this->belongsTo('\App\FishFamily', 'fish_families_id', 'id');
  }

  public function records()
    {
      return $this->hasMany('\App\Record', 'fish_breeds_id', 'id');
    }
}
