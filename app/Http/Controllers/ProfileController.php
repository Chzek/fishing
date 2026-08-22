<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Crew;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $angler = Angler::where('user_id', Auth::id())->first();

        if (isset($angler->id)) {
            $records = Record::where('anglers_id', $angler->id)
                ->orderBy('caught', 'desc')
                ->get()
                ->groupBy('caught');

            $crews = Crew::where('anglers_id', $angler->id)->count();

            $personalBest = [
                'byLength' => PersonalBestController::bestByLength($angler),
                'byWeight' => PersonalBestController::bestByWeight($angler),
                'lakeWithMostCatches' => PersonalBestController::lakeWithMostCatches($angler),
            ];

            $record_count = Record::where('anglers_id', $angler->id)->count();
            $lake_count = Record::where('anglers_id', $angler->id)
                ->distinct('lakes_id')
                ->count('lakes_id');

            $totalInches = round(Record::where('anglers_id', $angler->id)->sum('length'), 1);
            $totalFeet = round($totalInches / 12, 1);
            $avgLength = round(Record::where('anglers_id', $angler->id)->whereNotNull('length')->avg('length'), 1);
            $releasedCount = Record::where('anglers_id', $angler->id)->where('released', 1)->count();
            $releaseRate = $record_count > 0 ? round(($releasedCount / $record_count) * 100) : 0;

            $mvpLure = Record::select('lures_id', DB::raw('count(*) as catches'), DB::raw('max(length) as longest'))
                ->where('anglers_id', $angler->id)
                ->whereNotNull('lures_id')
                ->groupBy('lures_id')
                ->orderBy('catches', 'desc')
                ->with('lure')
                ->first();

            $peakMonth = Record::select(DB::raw('month(caught) as month_num'), DB::raw('count(*) as count'))
                ->where('anglers_id', $angler->id)
                ->whereNotNull('caught')
                ->groupBy(DB::raw('month(caught)'))
                ->orderBy('count', 'desc')
                ->first();

            $peakMonthName = $peakMonth ? \DateTime::createFromFormat('!m', $peakMonth->month_num)->format('F') : null;

            $topWaters = Record::select('lakes_id', DB::raw('count(*) as catches'), DB::raw('max(length) as longest'))
                ->where('anglers_id', $angler->id)
                ->whereNotNull('lakes_id')
                ->groupBy('lakes_id')
                ->orderBy('catches', 'desc')
                ->take(3)
                ->with('lake')
                ->get();

            $speciesDistribution = Record::select('fish_breeds_id', DB::raw('count(*) as count'))
                ->where('anglers_id', $angler->id)
                ->whereNotNull('fish_breeds_id')
                ->groupBy('fish_breeds_id')
                ->orderBy('count', 'desc')
                ->with('fishBreed')
                ->get();
        } else {
            $records = [];
            $crews = 0;
            $personalBest = [];
            $record_count = 0;
            $lake_count = 0;
            $totalInches = 0;
            $totalFeet = 0;
            $avgLength = 0;
            $releasedCount = 0;
            $releaseRate = 0;
            $mvpLure = null;
            $peakMonthName = null;
            $topWaters = collect();
            $speciesDistribution = collect();
        }

        return view('profile.show', [
            'angler' => $angler,
            'records' => $records,
            'crews' => $crews,
            'personalBest' => $personalBest,
            'record_count' => $record_count,
            'lake_count' => $lake_count,
            'totalInches' => $totalInches,
            'totalFeet' => $totalFeet,
            'avgLength' => $avgLength,
            'releasedCount' => $releasedCount,
            'releaseRate' => $releaseRate,
            'mvpLure' => $mvpLure,
            'peakMonthName' => $peakMonthName,
            'topWaters' => $topWaters,
            'speciesDistribution' => $speciesDistribution,
        ]);
    }

    /**
     * Edit the authenticated users profile
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the authenticated user's profile and password.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match your account password.']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect('/profile/edit')->with('status', 'Account preferences and password updated successfully.');
    }
}

