<?php

namespace Fishinglog\Http\Controllers\Admin;

use Fishinglog\Http\Controllers\Controller;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\FishFamily;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Post;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $anglers = Angler::count();
        $lakes = Lake::count();
        $expeditions = Expedition::count();
        $fishBreeds = FishBreed::count();
        $fishFamilies = FishFamily::count();
        $records = Record::count();
        $users = User::count();
        $lures = Lure::count();
        $posts = Post::count();
        $years = Record::count(DB::raw('distinct year(caught)'));

        return view('admin.index', [
            'anglers' => $anglers,
            'lakes' => $lakes,
            'expeditions' => $expeditions,
            'fishBreeds' => $fishBreeds,
            'fishFamilies' => $fishFamilies,
            'records' => $records,
            'users' => $users,
            'lures' => $lures,
            'posts' => $posts,
            'years' => $years,
        ]);
    }
}
