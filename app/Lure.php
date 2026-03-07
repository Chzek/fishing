<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lure extends Model
{
  use HasFactory;

  //
  public function records()
  {
    return $this->hasMany('\Fishinglog\Record', 'lures_id');
  }
  
  public function getDisplayNameAttribute()
  {
      return "{$this->name}, {$this->color} {$this->size}";
  }
}
