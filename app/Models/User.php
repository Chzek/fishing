<?php

namespace Fishinglog\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property-read \Fishinglog\Models\Angler|null $angler
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasFactory;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    const ADMIN_TYPE = 'admin';
    const DEFAULT_TYPE = 'default';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id', 'sync_status', 'synced_at', 'name', 'email', 'password', 'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Check to see if this user is an Admin
     * 
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->type === self::ADMIN_TYPE;
    }

    /**
     * Check to see if the user has been Registered
     * 
     * @return boolean
     */
    public function isRegistered()
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Return the Angler associated with this user
     */
    public function angler(): HasOne
    {
        return $this->hasOne(Angler::class, 'user_id');
    }
}
