<?php

namespace Fishinglog\Models;

use Fishinglog\Traits\HasUuidAndSyncTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string|null $expeditions_id
 * @property \Illuminate\Support\Carbon|null $entry_date
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string $title
 * @property string $body_markdown
 * @property string|null $highlights
 * @property string|null $weather_summary
 * @property string|null $location_summary
 * @property string $template_type
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Fishinglog\Models\Expedition|null $expedition
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\JournalPage> $pages
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Angler> $anglers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Lake> $lakes
 */
class JournalEntry extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuidAndSyncTracking;

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'expeditions_id',
        'entry_date',
        'start_date',
        'end_date',
        'title',
        'body_markdown',
        'highlights',
        'weather_summary',
        'location_summary',
        'template_type',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'synced_at' => 'datetime',
        ];
    }

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class, 'expeditions_id', 'id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(JournalPage::class, 'journal_entry_id', 'id')->orderBy('sequence_order');
    }

    public function anglers(): BelongsToMany
    {
        return $this->belongsToMany(Angler::class, 'journal_entry_anglers', 'journal_entry_id', 'angler_id')->withTimestamps();
    }

    public function lakes(): BelongsToMany
    {
        return $this->belongsToMany(Lake::class, 'journal_entry_lakes', 'journal_entry_id', 'lake_id')->withTimestamps();
    }
}
