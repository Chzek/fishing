<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lure extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['id', 'sync_status', 'synced_at', 'name', 'color', 'size'];

    public function record()
    {
        return $this->hasOne(Record::class);
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->name}, {$this->color} {$this->size}";
    }
}
