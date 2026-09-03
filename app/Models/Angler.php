<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string|null $firstName
 * @property string|null $middleName
 * @property string|null $lastName
 * @property string|null $firstname
 * @property string|null $middlename
 * @property string|null $lastname
 * @property string|null $name
 * @property int|null $user_id
 * @property string|null $birthdate
 * @property string|null $avatar
 * @property float|null $avg_length
 * @property float|null $avg_weight
 * @property float|null $release_rate
 * @property string|null $top_species_name
 * @property-read string $full_name
 * @property-read string $formal_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Record> $records
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Crew> $crews
 * @property-read \Fishinglog\Models\User|null $user
 */
class Angler extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id', 'sync_status', 'synced_at', 'firstName', 'middleName', 'lastName', 'firstname', 'middlename', 'lastname', 'name', 'user_id',
    ];

    public function records(): HasMany
    {
        return $this->hasMany(Record::class, 'anglers_id', 'id');
    }

    public function crews(): HasMany
    {
        return $this->hasMany(Crew::class, 'anglers_id', 'id');
    }

    public function lakes(): HasManyThrough
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

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getFullNameAttribute()
    {
        $trimmedMiddle = trim($this->middleName ?? '');
        $middleInitial = ($trimmedMiddle !== '' && !in_array($trimmedMiddle, ['?', 'N/A'])) 
            ? ' ' . strtoupper(mb_substr($trimmedMiddle, 0, 1)) . '.'
            : '';
        return trim("{$this->firstName}{$middleInitial} {$this->lastName}");
    }

    public function getFormalNameAttribute()
    {
        $trimmedMiddle = trim($this->middleName ?? '');
        $middleInitial = ($trimmedMiddle !== '' && !in_array($trimmedMiddle, ['?', 'N/A'])) 
            ? ' ' . strtoupper(mb_substr($trimmedMiddle, 0, 1)) . '.'
            : '';
        return trim("{$this->lastName}, {$this->firstName}{$middleInitial}");
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

    /**
     * Get all photos attached directly to this angler.
     */
    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable')->orderBy('created_at', 'desc');
    }

    /**
     * Set avatar from an existing Photo model.
     */
    public function setAvatarFromPhoto(Photo $photo): bool
    {
        if (empty($photo->path)) {
            return false;
        }

        $this->avatar = $photo->path;
        return $this->save();
    }
}
