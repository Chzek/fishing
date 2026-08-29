<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishBreed extends Model
{
    use HasFactory;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['id', 'sync_status', 'synced_at', 'name', 'fish_families_id', 'image', 'avatar'];

    public function family()
    {
        return $this->belongsTo(FishFamily::class, 'fish_families_id', 'id');
    }

    public function records()
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

