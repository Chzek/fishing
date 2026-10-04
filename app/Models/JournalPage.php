<?php

namespace Fishinglog\Models;

use Fishinglog\Traits\HasUuidAndSyncTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string|null $journal_entry_id
 * @property int|null $page_number
 * @property int $sequence_order
 * @property string $filename
 * @property string $photo_path
 * @property string|null $raw_ocr_text
 * @property array<string, mixed>|null $structured_metadata
 * @property bool $is_processed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Fishinglog\Models\JournalEntry|null $journalEntry
 * @property-read string $url
 */
class JournalPage extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuidAndSyncTracking;

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'journal_entry_id',
        'page_number',
        'sequence_order',
        'filename',
        'photo_path',
        'raw_ocr_text',
        'structured_metadata',
        'is_processed',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'page_number' => 'integer',
            'sequence_order' => 'integer',
            'structured_metadata' => 'array',
            'is_processed' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id', 'id');
    }

    /**
     * Get the public or storage URL for this journal page photo.
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->photo_path)) {
            return '';
        }

        if (str_starts_with($this->photo_path, 'http://') || str_starts_with($this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        return Storage::disk('public')->url($this->photo_path);
    }
}
