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

    public function index(\Fishinglog\Services\NasSyncService $syncService)
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
        $weatherJoinedRecordsCount = DB::table('records')
            ->join('lake_daily_weather', function ($join) {
                $join->on('records.lakes_id', '=', 'lake_daily_weather.lakes_id')
                     ->on(DB::raw('DATE(records.caught)'), '=', 'lake_daily_weather.date');
            })
            ->whereNull('records.deleted_at')
            ->count();

        $weatherCoverageRate = $records > 0 ? round(($weatherJoinedRecordsCount / $records) * 100) : 0;

        $pendingWeatherSyncCount = DB::table('records')
            ->join('lakes', 'records.lakes_id', '=', 'lakes.id')
            ->leftJoin('lake_daily_weather', function ($join) {
                $join->on('records.lakes_id', '=', 'lake_daily_weather.lakes_id')
                     ->on(DB::raw('DATE(records.caught)'), '=', 'lake_daily_weather.date');
            })
            ->whereNull('records.deleted_at')
            ->whereNull('lakes.deleted_at')
            ->whereNotNull('lakes.latitude')
            ->whereNotNull('lakes.longitude')
            ->whereNull('lake_daily_weather.id')
            ->count();

        $missingCoordsRecordsCount = DB::table('records')
            ->join('lakes', 'records.lakes_id', '=', 'lakes.id')
            ->leftJoin('lake_daily_weather', function ($join) {
                $join->on('records.lakes_id', '=', 'lake_daily_weather.lakes_id')
                     ->on(DB::raw('DATE(records.caught)'), '=', 'lake_daily_weather.date');
            })
            ->whereNull('records.deleted_at')
            ->whereNull('lakes.deleted_at')
            ->where(function ($q) {
                $q->whereNull('lakes.latitude')->orWhereNull('lakes.longitude');
            })
            ->whereNull('lake_daily_weather.id')
            ->count();

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
            'years' => $years > 0 ? $years : 1,
            'trashedCount' => Record::onlyTrashed()->count() + Lake::onlyTrashed()->count() + Angler::onlyTrashed()->count() + Lure::onlyTrashed()->count() + Expedition::onlyTrashed()->count(),
            'pendingSyncCount' => $syncService->getPendingCount(),
            'pendingSyncBreakdown' => $syncService->getPendingBreakdown(),
            'lastSyncedAt' => $syncService->getLastSyncedAt(),
            'weatherJoinedRecordsCount' => $weatherJoinedRecordsCount,
            'weatherCoverageRate' => $weatherCoverageRate,
            'pendingWeatherSyncCount' => $pendingWeatherSyncCount,
            'missingCoordsRecordsCount' => $missingCoordsRecordsCount,
        ]);
    }

    public function triggerSync(\Fishinglog\Services\NasSyncService $syncService)
    {
        try {
            $result = $syncService->sync();
            $pushedDetails = !empty($result['pushed_breakdown'])
                ? ' (' . collect($result['pushed_breakdown'])->map(fn($c, $k) => "$c $k")->join(', ') . ')'
                : '';
            $pulledDetails = !empty($result['pulled_breakdown'])
                ? ' (' . collect($result['pulled_breakdown'])->map(fn($c, $k) => "$c $k")->join(', ') . ')'
                : '';

            return redirect()->route('admin')->with('status', "NAS Sync completed! Pushed {$result['pushed']} items{$pushedDetails}, pulled {$result['pulled']} items{$pulledDetails}.");
        } catch (\Throwable $e) {
            return redirect()->route('admin')->with('error', "NAS Sync failed: {$e->getMessage()}");
        }
    }

    public function triggerWeatherSync()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('weather:sync');
            return redirect()->route('admin')->with('status', 'Weather Telemetry Sync triggered successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin')->with('error', "Weather sync failed: {$e->getMessage()}");
        }
    }

    public function users()
    {
        $users = User::with('angler')->orderBy('name', 'asc')->get();
        $anglers = Angler::orderBy('lastName', 'asc')->get();

        return view('admin.users.index', [
            'users' => $users,
            'anglers' => $anglers,
        ]);
    }

    public function linkAngler(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'angler_id' => 'nullable|exists:anglers,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Disassociate current angler if any
        Angler::where('user_id', $user->id)->update(['user_id' => null]);

        if ($request->angler_id) {
            $angler = Angler::findOrFail($request->angler_id);
            $angler->user_id = $user->id;
            $angler->save();
        }

        return redirect()->route('admin.users')->with('status', "Angler assignment updated for user {$user->name}.");
    }

    public function toggleAdmin(User $user)
    {
        // Protect preventing current logged in admin from demoting themselves accidentally
        if (auth()->id() === $user->id && $user->isAdmin()) {
            return redirect()->route('admin.users')->with('error', 'You cannot revoke your own admin rights.');
        }

        $user->type = $user->isAdmin() ? User::DEFAULT_TYPE : User::ADMIN_TYPE;
        $user->save();

        return redirect()->route('admin.users')->with('status', "Admin permissions updated for user {$user->name}.");
    }

    public function deleteUser(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account from the admin panel.');
        }

        // Unlink associated angler if any
        Angler::where('user_id', $user->id)->update(['user_id' => null]);

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')->with('status', "User account {$name} has been removed successfully.");
    }

    public function verifyUser(User $user)
    {
        if (!$user->isRegistered()) {
            $user->markEmailAsVerified();
            return redirect()->route('admin.users')->with('status', "User {$user->name} has been manually verified.");
        }

        return redirect()->route('admin.users')->with('status', "User {$user->name} is already verified.");
    }

    public function trash()
    {
        return view('admin.trash.index', [
            'trashedCatches' => Record::onlyTrashed()->with(['angler', 'lake', 'fishBreed'])->get(),
            'trashedLakes' => Lake::onlyTrashed()->get(),
            'trashedAnglers' => Angler::onlyTrashed()->get(),
            'trashedLures' => Lure::onlyTrashed()->get(),
            'trashedExpeditions' => Expedition::onlyTrashed()->get(),
        ]);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'type' => 'required|in:record,lake,angler,lure,expedition',
            'id' => 'required|string',
        ]);

        $modelClass = $this->getModelClass($request->type);
        $item = $modelClass::onlyTrashed()->findOrFail($request->id);
        $item->restore();

        return redirect()->route('admin.trash')->with('status', 'Item successfully restored from trash.');
    }

    public function forceDelete(Request $request)
    {
        $request->validate([
            'type' => 'required|in:record,lake,angler,lure,expedition',
            'id' => 'required|string',
        ]);

        $modelClass = $this->getModelClass($request->type);
        $item = $modelClass::onlyTrashed()->findOrFail($request->id);
        $item->forceDelete();

        return redirect()->route('admin.trash')->with('status', 'Item permanently deleted.');
    }

    protected function getModelClass(string $type): string
    {
        return match ($type) {
            'record' => Record::class,
            'lake' => Lake::class,
            'angler' => Angler::class,
            'lure' => Lure::class,
            'expedition' => Expedition::class,
        };
    }
}
