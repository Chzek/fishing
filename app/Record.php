<?php

namespace Fishinglog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Record extends Model
{
	use HasFactory;

	//
	public function angler()
	{
		return $this->belongsTo('\Fishinglog\Angler', 'anglers_id', 'id');
	}

	//
	public function lake()
	{
		return $this->belongsTo('\Fishinglog\Lake', 'lakes_id', 'id');
	}

	//
	public function fishBreed()
	{
		return $this->belongsTo('\Fishinglog\FishBreed', 'fish_breeds_id', 'id');
	}

	//
	public function lure()
	{
		return $this->belongsTo('\Fishinglog\Lure', 'lures_id', 'id');
	}

	public function expedition()
	{
		return $this->belongsTo('\Fishinglog\Expedition', 'id');
	}
}
