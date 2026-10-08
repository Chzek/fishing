<?php

namespace Fishinglog\Models;

use Fishinglog\Traits\HasUuidAndSyncTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $anglers_id
 * @property string $badge_id
 * @property string|null $record_id
 * @property string|null $expedition_id
 * @property \Illuminate\Support\Carbon $awarded_at
 * @property int $points
 * @property string|null $trigger_summary
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Fishinglog\Models\Angler $angler
 * @property-read \Fishinglog\Models\Badge $badge
 * @property-read \Fishinglog\Models\Record|null $record
 * @property-read \Fishinglog\Models\Expedition|null $expedition
 */
class AnglerBadge extends Model
{
    use HasFactory;
    use HasUuidAndSyncTracking;

    /**
     * @var string
     */
    protected $table = 'angler_badges';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'anglers_id',
        'badge_id',
        'record_id',
        'expedition_id',
        'awarded_at',
        'points',
        'trigger_summary',
        'sync_status',
        'synced_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'awarded_at' => 'datetime',
            'synced_at' => 'datetime',
            'points' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Angler, $this>
     */
    public function angler(): BelongsTo
    {
        return $this->belongsTo(Angler::class, 'anglers_id');
    }

    /**
     * @return BelongsTo<Badge, $this>
     */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }

    /**
     * @return BelongsTo<Record, $this>
     */
    public function record(): BelongsTo
    {
        return $this->belongsTo(Record::class, 'record_id');
    }

    /**
     * @return BelongsTo<Expedition, $this>
     */
    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class, 'expedition_id');
    }
}
