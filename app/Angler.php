<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;

class Angler extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'middlename', 'lastname', 'user_id'
    ];

    public function records()
    {
        return $this->hasMany('\Fishinglog\Record', 'anglers_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->lastName}, {$this->firstName} {$this->middleName}";
    }

    public function personal_best(){
        return $this->records()->orderBy('length', 'desc')->first();
    }

    public function personal_best_breed(fishinglog\FishBreed $breed)
    {
        return $this->records()->where('fish_breeds_id', $breed->id)->orderBy('length', 'desc')->first();
    }
}
