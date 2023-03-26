<?php

namespace Fishinglog;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasFactory;

    const ADMIN_TYPE = 'admin';
    const DEFAULT_TYPE = 'default';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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
        return $this->hasOne('\Fishinglog\Angler', 'user_id');
    }
}
