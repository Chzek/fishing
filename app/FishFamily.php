<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FishFamily extends Model
{
	use HasFactory;
    //
	public function breeds()
	{
		return $this->hasMany('\Fishinglog\FishBreed', 'fish_families_id', 'fish_families_id');
	}
}
