<?php

namespace Fishinglog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FishBreed extends Model
{
    use HasFactory;
    use \Fishinglog\Traits\HasUuidAndSyncTracking;

    protected $fillable = ['id', 'sync_status', 'synced_at', 'name', 'fish_families_id', 'image'];

    public function family()
    {
        return $this->belongsTo(FishFamily::class, 'fish_families_id', 'id');
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'fish_breeds_id', 'id');
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
