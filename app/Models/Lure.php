<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string $name
 * @property string|null $color
 * @property string|null $size
 * @property string|null $category
 * @property string|null $brand
 * @property string|null $weight
 * @property string|null $depth_range
 * @property-read string $display_name
 * @property-read \Fishinglog\Models\Record|null $record
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Record> $records
 */
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

    public function record(): HasOne
    {
        return $this->hasOne(Record::class, 'lures_id', 'id');
    }

    public function records(): HasMany
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

