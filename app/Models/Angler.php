<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Angler extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'sync_status', 'synced_at', 'firstName', 'middleName', 'lastName', 'firstname', 'middlename', 'lastname', 'name', 'user_id',
    ];

    public function records()
    {
        return $this->hasMany(Record::class, 'anglers_id', 'id');
    }

    public function lakes()
    {
        return $this->hasManyThrough(
            Lake::class,
            Record::class,
            'anglers_id',
            'id',
            'id',
            'id'
        );
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
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
