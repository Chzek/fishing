<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    public function expedition()
    {
        return $this->belongsTo(\Fishinglog\Expedition::class, 'expeditions_id');
    }
}
