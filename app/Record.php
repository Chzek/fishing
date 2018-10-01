<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
		//
		public function angler()
		{
			return $this->belongsTo('\App\Angler', 'anglers_id', 'id');
		}

		//
		public function lake()
		{
			return $this->belongsTo('\App\Lake', 'lakes_id', 'id');
		}

		//
		public function fishBreed()
		{
			return $this->belongsTo('\App\FishBreed', 'fish_breeds_id', 'id');
		}

		//
		public function lure()
		{
			return $this->belongsTo('\App\Lure', 'lures_id', 'id');
		}

		public function expedition()
		{
			return $this->belongsTo('\App\Expedition', 'id');
		}
}
