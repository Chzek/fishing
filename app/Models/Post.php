<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['uuid', 'sync_status', 'synced_at', 'expeditions_id', 'anglers_id', 'title', 'body'];

    public function expedition()
    {
        return $this->belongsTo(Expedition::class, 'expeditions_id');
    }

    public function creator()
    {
        return $this->belongsTo(Angler::class, 'anglers_id');
    }
}
