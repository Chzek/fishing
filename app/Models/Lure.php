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

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'name',
        'color',
        'size',
        'category',
        'brand',
        'weight',
        'depth_range',
    ];

    public function record()
    {
        return $this->hasOne(Record::class, 'lures_id', 'id');
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'lures_id', 'id');
    }

    public function scopeCategory($query, $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function getDisplayNameAttribute()
    {
        $brandText = $this->brand ? "{$this->brand} " : '';
        $specs = array_filter([$this->color, $this->size ?: $this->weight, $this->depth_range]);
        $specText = !empty($specs) ? ' (' . implode(' • ', $specs) . ')' : '';

        return "{$brandText}{$this->name}{$specText}";
    }
}

