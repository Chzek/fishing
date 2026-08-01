<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public function expedition()
    {
        return $this->belongsTo(Expedition::class, 'expeditions_id');
    }

    public function creator()
    {
        return $this->belongsTo(Angler::class, 'anglers_id');
    }
}
