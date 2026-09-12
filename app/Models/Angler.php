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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'synced_at' => 'datetime',
            'birthdate' => 'date',
        ];
    }

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

    public function getFirstnameAttribute(): ?string
    {
        return $this->attributes['firstName'] ?? $this->attributes['firstname'] ?? null;
    }

    public function getLastnameAttribute(): ?string
    {
        return $this->attributes['lastName'] ?? $this->attributes['lastname'] ?? null;
    }

    public function getMiddlenameAttribute(): ?string
    {
        return $this->attributes['middleName'] ?? $this->attributes['middlename'] ?? null;
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function getFullNameAttribute(): string
    {
        $first = $this->firstName ?? $this->firstname ?? '';
        $middle = $this->middleName ?? $this->middlename ?? '';
        $last = $this->lastName ?? $this->lastname ?? '';

        $trimmedMiddle = trim($middle);
        $middleInitial = ($trimmedMiddle !== '' && !in_array($trimmedMiddle, ['?', 'N/A'])) 
            ? ' ' . strtoupper(mb_substr($trimmedMiddle, 0, 1)) . '.'
            : '';
        return trim("{$first}{$middleInitial} {$last}");
    }

    public function getFormalNameAttribute(): string
    {
        $first = $this->firstName ?? $this->firstname ?? '';
        $middle = $this->middleName ?? $this->middlename ?? '';
        $last = $this->lastName ?? $this->lastname ?? '';

        $trimmedMiddle = trim($middle);
        $middleInitial = ($trimmedMiddle !== '' && !in_array($trimmedMiddle, ['?', 'N/A'])) 
            ? ' ' . strtoupper(mb_substr($trimmedMiddle, 0, 1)) . '.'
            : '';
        return trim("{$last}, {$first}{$middleInitial}");
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
     * Get the lake where this angler has recorded the most catches.
     */
    public function lakeWithMostCatches(): ?Lake
    {
        $topLake = Record::select('lakes_id', \Illuminate\Support\Facades\DB::raw('count(id) as total'))
            ->where('anglers_id', $this->id)
            ->whereNotNull('lakes_id')
            ->groupBy('lakes_id')
            ->orderBy('total', 'desc')
            ->first();

        return $topLake ? Lake::find($topLake->lakes_id) : null;
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
