<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string|null $sync_status
 * @property \Illuminate\Support\Carbon|null $synced_at
 * @property string $name
 * @property string|null $fish_families_id
 * @property string|null $image
 * @property string|null $avatar
 * @property float|null $trophy_length_bench
 * @property float|null $trophy_weight_bench
 * @property-read \Fishinglog\Models\FishFamily|null $family
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Fishinglog\Models\Record> $records
 * @property-read string|null $avatar_url
 * @property-read string|null $image_url
 */
class FishBreed extends Model
{
    use HasFactory;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = [
        'id',
        'sync_status',
        'synced_at',
        'name',
        'fish_families_id',
        'image',
        'avatar',
        'trophy_length_bench',
        'trophy_weight_bench',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'synced_at' => 'datetime',
            'trophy_length_bench' => 'float',
            'trophy_weight_bench' => 'float',
        ];
    }

    /**
     * Get effective Master Angler benchmark length with fallback.
     */
    public function getBenchmarkLength(): float
    {
        if ($this->trophy_length_bench && $this->trophy_length_bench > 0) {
            return (float) $this->trophy_length_bench;
        }

        $nameLower = strtolower(trim($this->name));
        $defaults = [
            'rock bass' => 10.0,
            'largemouth bass' => 20.0,
            'smallmouth bass' => 20.0,
            'northern pike' => 36.0,
            'muskellunge' => 48.0,
            'lake trout' => 32.0,
            'brook trout' => 18.0,
            'splake' => 22.0,
            'brown trout' => 24.0,
            'bluegill' => 9.5,
            'walleye' => 28.0,
            'yellow perch' => 12.0,
            'perch' => 12.0,
            'crappie' => 13.0,
            'rainbow' => 26.0,
            'steelhead' => 26.0,
            'chinook' => 34.0,
            'king' => 34.0,
            'atlantic' => 30.0,
            'choho' => 28.0,
            'coho' => 28.0,
        ];

        foreach ($defaults as $key => $len) {
            if (str_contains($nameLower, $key)) {
                return (float) $len;
            }
        }

        return 20.0;
    }

    /**
     * Get effective Master Angler benchmark weight with fallback.
     */
    public function getBenchmarkWeight(): float
    {
        if ($this->trophy_weight_bench && $this->trophy_weight_bench > 0) {
            return (float) $this->trophy_weight_bench;
        }

        $nameLower = strtolower(trim($this->name));
        $defaults = [
            'rock bass' => 0.75,
            'largemouth bass' => 5.00,
            'smallmouth bass' => 4.50,
            'northern pike' => 15.00,
            'muskellunge' => 30.00,
            'lake trout' => 15.00,
            'brook trout' => 3.50,
            'splake' => 5.00,
            'brown trout' => 6.00,
            'bluegill' => 0.85,
            'walleye' => 8.00,
            'yellow perch' => 1.25,
            'perch' => 1.25,
            'crappie' => 1.50,
            'rainbow' => 8.00,
            'steelhead' => 8.00,
            'chinook' => 18.00,
            'king' => 18.00,
            'atlantic' => 10.00,
            'choho' => 10.00,
            'coho' => 10.00,
        ];

        foreach ($defaults as $key => $w) {
            if (str_contains($nameLower, $key)) {
                return (float) $w;
            }
        }

        return 5.0;
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(FishFamily::class, 'fish_families_id', 'id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class, 'fish_breeds_id', 'id');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar)) {
            return null;
        }

        $base = pathinfo($this->avatar, PATHINFO_FILENAME);

        foreach (['svg', 'png', 'jpg', 'jpeg'] as $ext) {
            if (file_exists(public_path('images/fish/avatars/' . $base . '.' . $ext))) {
                return asset('images/fish/avatars/' . $base . '.' . $ext);
            }
            if (file_exists(public_path('images/fish/' . $base . '.' . $ext))) {
                return asset('images/fish/' . $base . '.' . $ext);
            }
            if (file_exists(storage_path('app/public/fish/avatars/' . $base . '.' . $ext))) {
                return asset('storage/fish/avatars/' . $base . '.' . $ext);
            }
            if (file_exists(storage_path('app/public/fish/' . $base . '.' . $ext))) {
                return asset('storage/fish/' . $base . '.' . $ext);
            }
        }

        return null;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (file_exists(public_path('images/fish/' . $this->image))) {
            return asset('images/fish/' . $this->image);
        }

        if (file_exists(public_path('images/fish/' . $this->image . '.jpg'))) {
            return asset('images/fish/' . $this->image . '.jpg');
        }

        if (file_exists(storage_path('app/public/fish/' . $this->image))) {
            return asset('storage/fish/' . $this->image);
        }

        return null;
    }
}

