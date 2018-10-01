<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FishFamily extends Model
{
    //
		public function breeds()
		{
			return $this->hasMany('\App\FishBreed', 'fish_families_id', 'fish_families_id');
		}
}
