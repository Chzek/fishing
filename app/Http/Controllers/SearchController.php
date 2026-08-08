<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle global omnibox search and command palette queries.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = trim($request->input('q', $request->input('search', '')));

        // 1. Static Action Commands Definition
        $allActions = [
            [
                'title' => 'Launch Quick Catch Mode',
                'description' => 'Touch-optimized mobile boat catch logger',
                'url' => url('/record/quick'),
                'icon' => 'zap',
                'aliases' => ['quick catch', 'new catch', 'log catch', 'add catch', 'boat catch'],
            ],
            [
                'title' => 'Create New Angler Profile',
                'description' => 'Register a new crew angler',
                'url' => url('/angler/create'),
                'icon' => 'user-plus',
                'aliases' => ['new angler', 'create angler', 'add angler', 'register angler'],
            ],
            [
                'title' => 'Register New Lake & Waterbody',
                'description' => 'Add coordinates, depth, and structure pin',
                'url' => url('/lake/create'),
                'icon' => 'waves',
                'aliases' => ['new lake', 'create lake', 'add lake', 'register lake'],
            ],
            [
                'title' => 'Add Lure or Tackle Gear',
                'description' => 'Catalog a new lure or bait',
                'url' => url('/lure/create'),
                'icon' => 'fishing-hook',
                'aliases' => ['new lure', 'add lure', 'create lure', 'tackle'],
            ],
            [
                'title' => 'Create New Fishing Expedition',
                'description' => 'Start a multi-day fishing trip journal',
                'url' => url('/expedition/create'),
                'icon' => 'ship',
                'aliases' => ['new expedition', 'create expedition', 'add expedition', 'new trip'],
            ],
            [
                'title' => 'Offline Map Tile Downloader',
                'description' => 'Pre-download Wawa hydrographic map tiles',
                'url' => url('/map/offline'),
                'icon' => 'map',
                'aliases' => ['offline maps', 'download maps', 'map tiles', 'wawa map'],
            ],
            [
                'title' => 'Interactive Map Explorer',
                'description' => 'Dynamic viewport bounding-box lake map',
                'url' => url('/map/explorer'),
                'icon' => 'compass',
                'aliases' => ['map explorer', 'lake map', 'explorer'],
            ],
            [
                'title' => 'Angler Dashboard & Personal Stats',
                'description' => 'View personal records and trip logs',
                'url' => url('/profile'),
                'icon' => 'layout-dashboard',
                'aliases' => ['dashboard', 'profile', 'my stats', 'stats'],
            ],
        ];

        $matchedActions = [];
        if (!empty($query)) {
            $lowercaseQuery = strtolower($query);
            foreach ($allActions as $action) {
                if (str_contains(strtolower($action['title']), $lowercaseQuery)) {
                    $matchedActions[] = $action;
                    continue;
                }
                foreach ($action['aliases'] as $alias) {
                    if (str_contains($alias, $lowercaseQuery) || str_contains($lowercaseQuery, $alias)) {
                        $matchedActions[] = $action;
                        break;
                    }
                }
            }
        } else {
            $matchedActions = array_slice($allActions, 0, 4);
        }

        // 2. Database Model Queries
        $anglers = collect();
        $lakes = collect();
        $fishBreeds = collect();
        $lures = collect();
        $expeditions = collect();
        $records = collect();

        if (!empty($query)) {
            $anglers = Angler::where('firstName', 'like', "%{$query}%")
                ->orWhere('lastName', 'like', "%{$query}%")
                ->orWhere('middleName', 'like', "%{$query}%")
                ->withCount('records')
                ->limit(5)
                ->get();

            $lakes = Lake::where('name', 'like', "%{$query}%")
                ->orWhere('structure', 'like', "%{$query}%")
                ->withCount('records')
                ->limit(5)
                ->get();

            $fishBreeds = FishBreed::where('name', 'like', "%{$query}%")
                ->with('family')
                ->limit(5)
                ->get();

            $lures = Lure::where('name', 'like', "%{$query}%")
                ->orWhere('color', 'like', "%{$query}%")
                ->orWhere('size', 'like', "%{$query}%")
                ->limit(5)
                ->get();

            $expeditions = Expedition::where('description', 'like', "%{$query}%")
                ->withCount('records')
                ->limit(5)
                ->get();

            $records = Record::with(['fishBreed', 'lake', 'angler'])
                ->where(function ($q) use ($query) {
                    $q->whereHas('fishBreed', fn($b) => $b->where('name', 'like', "%{$query}%"))
                      ->orWhereHas('lake', fn($l) => $l->where('name', 'like', "%{$query}%"))
                      ->orWhereHas('angler', fn($a) => $a->where('firstName', 'like', "%{$query}%")->orWhere('lastName', 'like', "%{$query}%"))
                      ->orWhereHas('lure', fn($lu) => $lu->where('name', 'like', "%{$query}%"));
                })
                ->orderBy('caught', 'desc')
                ->limit(5)
                ->get();
        }

        $totalMatches = count($matchedActions) + $anglers->count() + $lakes->count() + $fishBreeds->count() + $lures->count() + $expeditions->count() + $records->count();

        // Return JSON response if requested via AJAX/Alpine
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'query' => $query,
                'total' => $totalMatches,
                'actions' => $matchedActions,
                'anglers' => $anglers,
                'lakes' => $lakes,
                'fishBreeds' => $fishBreeds,
                'lures' => $lures,
                'expeditions' => $expeditions,
                'records' => $records,
            ]);
        }

        return view('search.index', [
            'query' => $query,
            'totalMatches' => $totalMatches,
            'matchedActions' => $matchedActions,
            'anglers' => $anglers,
            'lakes' => $lakes,
            'fishBreeds' => $fishBreeds,
            'lures' => $lures,
            'expeditions' => $expeditions,
            'records' => $records,
        ]);
    }
}
