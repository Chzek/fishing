<?php

namespace Fishinglog\Models;

use Fishinglog\Traits\HasUuidAndSyncTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property string $category
 * @property string $tier
 * @property int $points
 * @property string $icon
 * @property string|null $image_path
 * @property string $rule_type
 * @property float $rule_threshold
 * @property string $accent_color
 * @property int $sort_order
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Angler> $anglers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\AnglerBadge> $anglerBadges
 */
class Badge extends Model
{
    use HasFactory;
    use HasUuidAndSyncTracking;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'slug',
        'name',
        'description',
        'category',
        'tier',
        'points',
        'icon',
        'image_path',
        'rule_type',
        'rule_threshold',
        'accent_color',
        'sort_order',
        'sync_status',
        'synced_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'rule_threshold' => 'float',
            'sort_order' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\Fishinglog\Models\Angler, $this>
     */
    public function anglers(): BelongsToMany
    {
        return $this->belongsToMany(Angler::class, 'angler_badges', 'badge_id', 'anglers_id')
            ->withPivot(['id', 'record_id', 'expedition_id', 'awarded_at', 'points', 'trigger_summary', 'sync_status', 'synced_at'])
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Fishinglog\Models\AnglerBadge, $this>
     */
    public function anglerBadges(): HasMany
    {
        return $this->hasMany(AnglerBadge::class, 'badge_id');
    }

    /**
     * Get border color CSS classes corresponding to badge tier.
     */
    public function getTierBorderColor(): string
    {
        return match (strtolower($this->tier)) {
            'platinum' => 'border-cyan-400 shadow-cyan-500/30',
            'gold' => 'border-amber-400 shadow-amber-500/30',
            'silver' => 'border-slate-300 shadow-slate-400/30',
            default => 'border-amber-700 shadow-amber-900/30', // Bronze
        };
    }

    /**
     * Get badge tier badge pill color styling.
     */
    public function getTierBadgeClass(): string
    {
        return match (strtolower($this->tier)) {
            'platinum' => 'bg-cyan-950/80 text-cyan-300 border-cyan-500/50',
            'gold' => 'bg-amber-950/80 text-amber-300 border-amber-500/50',
            'silver' => 'bg-slate-800 text-slate-200 border-slate-500/50',
            default => 'bg-amber-900/50 text-amber-200 border-amber-700/60', // Bronze
        };
    }
}
