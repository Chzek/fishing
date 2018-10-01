<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lure extends Model
{
    //
    public function record()
    {
      return $this->hasOne('\App\Record');
    }
    
    public function getDisplayNameAttribute()
    {
        return "{$this->name}, {$this->color} {$this->size}";
    }
}
