<?php

namespace Fishinglog\Http\Controllers\Admin;

use Fishinglog\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		$anglers = \Fishinglog\Angler::count();
		$lakes = \Fishinglog\Lake::count();
		$expeditions = \Fishinglog\Expedition::count();
		$fishBreeds = \Fishinglog\FishBreed::count();
		$fishFamilies = \Fishinglog\FishFamily::count();
		$records = \Fishinglog\Record::count();
		$users = \Fishinglog\User::count();
		$lures = \Fishinglog\Lure::count();
		$posts =\Fishinglog\Post::count();

		return view('admin.index',[
			'anglers' => $anglers,
			'lakes' => $lakes,
			'expeditions' => $expeditions,
			'fishBreeds' => $fishBreeds,
			'fishFamilies' => $fishFamilies,
			'records' => $records,
			'users' => $users,
			'lures' => $lures,
			'posts' => $posts,
		]);
	}
}
