<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lure extends Model
{
  use HasFactory;

  //
  public function record()
  {
    return $this->hasOne('\Fishinglog\Record');
  }
  
  public function getDisplayNameAttribute()
  {
      return "{$this->name}, {$this->color} {$this->size}";
  }
}
