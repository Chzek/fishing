<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expedition extends Model
{
    use SoftDeletes;

    protected $fillable = ['description', 'title', 'start', 'finish'];

    public function crews()
    {
        return $this->hasMany(Crew::class, 'expeditions_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'expeditions_id', 'id');
    }

    public function records()
    {
        return $this->hasManyThrough(
            Record::class,
            Crew::class,
            'expeditions_id',
            'anglers_id',
            'id',
            'anglers_id'
        );
    }
}
