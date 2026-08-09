<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FishingZone extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'code',
        'name',
        'province_state',
        'country',
        'description',
        'regulations_url',
        'bounds',
    ];

    protected $casts = [
        'bounds' => 'array',
    ];

    public function lakes()
    {
        return $this->hasMany(Lake::class, 'fishing_zone_id', 'id');
    }

    public function rules()
    {
        return $this->hasMany(FishingRule::class, 'fishing_zone_id', 'id');
    }
}
