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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'synced_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('lure_categories'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('lure_categories'));
    }

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

    /**
     * Resolve Tailwind CSS gradient classes representing this lure's colorway.
     */
    public function getSwatchGradientAttribute(): string
    {
        return static::resolveSwatchGradient($this->color);
    }

    /**
     * Resolve realistic tactical gradient for a given colorway name.
     */
    public static function resolveSwatchGradient(?string $color): string
    {
        $colorName = strtolower(trim((string) $color));

        if (empty($colorName) || $colorName === 'standard' || $colorName === 'default') {
            return 'from-slate-400 via-slate-500 to-slate-600';
        }

        return match (true) {
            // Purple, Violet, Twilight, Pink, Bubblegum, Magenta
            str_contains($colorName, 'twilight') || str_contains($colorName, 'purple') || str_contains($colorName, 'violet') || str_contains($colorName, 'plum') => 'from-indigo-600 via-purple-600 to-pink-500',
            str_contains($colorName, 'pink') || str_contains($colorName, 'bubblegum') || str_contains($colorName, 'magenta') => 'from-pink-400 via-rose-500 to-pink-600',

            // Coppertreuse & Copper/Bronze/Brass
            str_contains($colorName, 'coppertreuse') => 'from-amber-600 via-yellow-500 to-lime-400',
            str_contains($colorName, 'copper') || str_contains($colorName, 'bronze') || str_contains($colorName, 'brass') || str_contains($colorName, 'rust') => 'from-amber-600 via-yellow-700 to-amber-900',

            // Mud, Dirt, Deal, Earth tones
            str_contains($colorName, 'mud') || str_contains($colorName, 'deal') || str_contains($colorName, 'dirt') || str_contains($colorName, 'brown') => 'from-stone-700 via-amber-900 to-slate-800',

            // Pumpkin, Green Pumpkin, Motor Oil, Avocado, Bison
            str_contains($colorName, 'pumpkin') || str_contains($colorName, 'motor oil') || str_contains($colorName, 'avocado') || str_contains($colorName, 'bison') => 'from-amber-700 via-emerald-800 to-stone-900',

            // Watermelon
            str_contains($colorName, 'watermelon') => 'from-emerald-500 via-emerald-700 to-rose-900',

            // Firetiger
            str_contains($colorName, 'firetiger') => 'from-lime-400 via-yellow-400 to-emerald-600',

            // Perch
            str_contains($colorName, 'perch') => 'from-amber-400 via-yellow-600 to-emerald-800',

            // Bluegill, Sunfish, Bream, Crappie
            str_contains($colorName, 'bluegill') || str_contains($colorName, 'sunfish') || str_contains($colorName, 'bream') || str_contains($colorName, 'crappie') || str_contains($colorName, 'gill') => 'from-teal-600 via-amber-600 to-indigo-900',

            // Craw, Crawfish, Lobster
            str_contains($colorName, 'craw') || str_contains($colorName, 'lobster') => 'from-rose-500 via-red-700 to-amber-900',

            // Red, Blood, Bleeding
            str_contains($colorName, 'red') || str_contains($colorName, 'blood') || str_contains($colorName, 'bleeding') || str_contains($colorName, 'ruby') || str_contains($colorName, 'crimson') => 'from-rose-500 via-red-600 to-rose-900',

            // Orange, Sunrise, Sunset
            str_contains($colorName, 'orange') || str_contains($colorName, 'sunrise') || str_contains($colorName, 'sunset') || str_contains($colorName, 'tangerine') => 'from-yellow-400 via-orange-500 to-rose-600',

            // Chartreuse, Lime, Lemon, Neon
            str_contains($colorName, 'chartreuse') || str_contains($colorName, 'lime') || str_contains($colorName, 'lemon') || str_contains($colorName, 'neon') => 'from-yellow-300 via-lime-400 to-emerald-500',

            // Trout, Rainbow, Steelhead, Brook
            str_contains($colorName, 'trout') || str_contains($colorName, 'rainbow') || str_contains($colorName, 'steelhead') => 'from-emerald-400 via-pink-400 to-amber-700',

            // Black, Dark, Night, Midnight, Obsidian, Loon
            str_contains($colorName, 'black') || str_contains($colorName, 'dark') || str_contains($colorName, 'night') || str_contains($colorName, 'midnight') || str_contains($colorName, 'obsidian') || str_contains($colorName, 'coal') || str_contains($colorName, 'loon') => 'from-slate-800 via-zinc-900 to-neutral-950',

            // Bone, White, Pearl, Ivory, Ghost, Clear
            str_contains($colorName, 'bone') || str_contains($colorName, 'white') || str_contains($colorName, 'pearl') || str_contains($colorName, 'ivory') || str_contains($colorName, 'ghost') || str_contains($colorName, 'clear') => 'from-slate-100 via-amber-50 to-slate-300',

            // Gold, Brass
            str_contains($colorName, 'gold') => 'from-yellow-200 via-amber-400 to-yellow-700',

            // Silver, Chrome, Nickel, Foil
            str_contains($colorName, 'silver') || str_contains($colorName, 'chrome') || str_contains($colorName, 'nickel') || str_contains($colorName, 'foil') => 'from-slate-200 via-sky-200 to-slate-400',

            // Blue, Indigo, Sapphire, Cobalt
            str_contains($colorName, 'blue') || str_contains($colorName, 'indigo') || str_contains($colorName, 'sapphire') || str_contains($colorName, 'cobalt') || str_contains($colorName, 'ocean') || str_contains($colorName, 'sky') => 'from-sky-300 via-blue-500 to-indigo-800',

            // Olive, Green, Moss, Kelp
            str_contains($colorName, 'olive') || str_contains($colorName, 'green') || str_contains($colorName, 'moss') || str_contains($colorName, 'kelp') => 'from-emerald-300 via-teal-700 to-slate-900',

            // Minnow, Shad, Smelt, Shiner, Alewife, Herring, Baitfish
            str_contains($colorName, 'minnow') || str_contains($colorName, 'shad') || str_contains($colorName, 'smelt') || str_contains($colorName, 'shiner') || str_contains($colorName, 'alewife') || str_contains($colorName, 'herring') || str_contains($colorName, 'baitfish') => 'from-slate-200 via-sky-400 to-slate-600',

            // Goby, Sculpin, Sand
            str_contains($colorName, 'goby') || str_contains($colorName, 'sculpin') || str_contains($colorName, 'sand') => 'from-stone-600 via-amber-800 to-stone-900',

            // Deterministic hash-based gradient for any other custom angler colorway
            default => static::hashToGradient($colorName),
        };
    }

    /**
     * Deterministic palette generator for unlisted custom colors.
     */
    protected static function hashToGradient(string $str): string
    {
        $palettes = [
            'from-teal-400 via-cyan-600 to-blue-800',
            'from-amber-400 via-orange-600 to-red-800',
            'from-lime-400 via-emerald-600 to-teal-800',
            'from-violet-400 via-purple-600 to-indigo-800',
            'from-fuchsia-400 via-pink-600 to-rose-800',
            'from-sky-400 via-blue-600 to-slate-800',
            'from-yellow-400 via-amber-600 to-stone-800',
        ];

        $hash = abs(crc32($str));
        return $palettes[$hash % count($palettes)];
    }
}

