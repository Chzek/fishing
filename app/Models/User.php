<?php

namespace Fishinglog\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
     * @var array
     */
    protected $fillable = [
        'uuid', 'sync_status', 'synced_at', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
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
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function angler()
    {
        return $this->hasOne(Angler::class, 'user_id');
    }
}
