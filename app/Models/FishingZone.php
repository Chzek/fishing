<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string $code
 * @property string $name
 * @property string $province_state
 * @property string $country
 * @property string|null $description
 * @property string|null $regulations_url
 * @property array<string, mixed>|null $bounds
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Lake> $lakes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\FishingRule> $rules
 */
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

    public function lakes(): HasMany
    {
        return $this->hasMany(Lake::class, 'fishing_zone_id', 'id');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(FishingRule::class, 'fishing_zone_id', 'id');
    }
}
