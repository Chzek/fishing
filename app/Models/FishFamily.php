<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishFamily extends Model
{
    use HasFactory;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['uuid', 'sync_status', 'synced_at', 'name'];

    public function breeds()
    {
        return $this->hasMany(FishBreed::class, 'fish_families_id', 'id');
    }
}
