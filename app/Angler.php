<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angler extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'middlename', 'lastname', 'user_id',
    ];

    public function records()
    {
        return $this->hasMany('\Fishinglog\Record', 'anglers_id', 'id');
    }

    public function lakes()
    {
        return $this->hasManyThrough(
            '\Fishinglog\Lake',
            '\Fishinglog\Record',
            'anglers_id',
            'id',
            'id',
            'id'
        );
    }

    public function user()
    {
        return $this->hasOne('\Fishinglog\User', 'id', 'user_id');
    }

    public function getFullNameAttribute()
    {
        return str_replace('?', '', "{$this->lastName}, {$this->firstName} {$this->middleName}");
    }

    public function personal_best()
    {
        return $this->records()
            ->orderBy('length', 'desc')
            ->first();
    }

    public function personal_best_breed(FishBreed $breed)
    {
        return $this->records()
            ->where('fish_breeds_id', $breed->id)
            ->orderBy('length', 'desc')
            ->first();
    }

    public function latest_catch()
    {
        return $this->records()
            ->orderBy('caught', 'desc')
            ->first();
    }
}
