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
        'firstname', 'middlename', 'lastname','user_id'
    ];

    public function records()
    {
        return $this->hasMany('\Fishinglog\Record', 'anglers_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->lastName}, {$this->firstName} {$this->middleName}";
    }
}
