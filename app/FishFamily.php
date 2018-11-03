<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;

class FishFamily extends Model
{
    //
		public function breeds()
		{
			return $this->hasMany('\Fishinglog\FishBreed', 'fish_families_id', 'fish_families_id');
		}
}
