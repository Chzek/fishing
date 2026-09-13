<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Http\Requests\StoreRecordRequest;
use Fishinglog\Http\Requests\UpdateRecordRequest;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\LakeDailyWeather;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Photo;
use Fishinglog\Models\Record;
use Fishinglog\Pipes\Filters\FilterByAngler;
use Fishinglog\Pipes\Filters\FilterByLength;
use Fishinglog\Pipes\Filters\FilterByName;
use Fishinglog\Pipes\Filters\FilterByRecordsCount;
use Fishinglog\Pipes\Filters\FilterBySearch;
use Fishinglog\Pipes\Filters\SortBy;
use Fishinglog\Actions\Records\CreateCatchRecordAction;
use Fishinglog\Actions\Media\ProcessPhotoUploadAction;
use Fishinglog\Services\CatchTelemetryService;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;


class RecordController extends Controller
{
    use Notifiable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Pipeline $pipeline, Request $request, CatchTelemetryService $telemetryService)
    {
        if ($request->hasAny(['search', 'length', 'length_operator', 'angler', 'sort_by', 'sort_order'])) {
            return redirect()->route('record.directory', $request->query());
        }

        $telemetry = $telemetryService->getOrCalculateTelemetry();

        return view('record.index', $telemetry);
    }

    /**
     * Display the Catches Logbook Directory table with search and filters.
     *
     * @param Pipeline $pipeline
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function directory(Pipeline $pipeline, Request $request)
    {
        $recordsQuery = Record::with(['angler', 'lake', 'fishBreed', 'lure'])
            ->orderBy('caught', 'desc')
            ->orderBy('lakes_id', 'asc')
            ->orderBy('anglers_id', 'asc');

        $filteredRecords = $pipeline->send($recordsQuery)
            ->through([
                SortBy::class,
                FilterBySearch::class,
                FilterByLength::class,
                FilterByAngler::class,
                \Fishinglog\Pipes\Filters\FilterBySpecies::class,
                \Fishinglog\Pipes\Filters\FilterByLake::class,
                \Fishinglog\Pipes\Filters\FilterByLure::class,
            ])
            ->thenReturn();

        $records = (clone $filteredRecords)->paginate(15)->withQueryString();
        $totalCount = (clone $filteredRecords)->count();

        // Optimizing weather telemetry: batch load exact daily weather records for current 15 page items
        $lakesAndDates = $records->getCollection()->map(function ($r) {
            $cDate = $r->caught instanceof \DateTimeInterface ? $r->caught->format('Y-m-d') : substr((string)$r->caught, 0, 10);
            return ['lake_id' => $r->lakes_id, 'date' => $cDate];
        })->filter(fn($item) => !empty($item['lake_id']) && !empty($item['date']))->unique();

        if ($lakesAndDates->isNotEmpty()) {
            $weatherModels = LakeDailyWeather::where(function ($query) use ($lakesAndDates) {
                foreach ($lakesAndDates as $item) {
                    $query->orWhere(function ($q) use ($item) {
                        $q->where('lakes_id', $item['lake_id'])->where('date', $item['date']);
                    });
                }
            })->get()->keyBy(fn($w) => $w->lakes_id . '_' . ($w->date instanceof \DateTimeInterface ? $w->date->format('Y-m-d') : substr((string)$w->date, 0, 10)));

            $records->getCollection()->each(function ($record) use ($weatherModels) {
                $cDate = $record->caught instanceof \DateTimeInterface ? $record->caught->format('Y-m-d') : substr((string)$record->caught, 0, 10);
                $record->setRelation('dailyWeather', $weatherModels->get($record->lakes_id . '_' . $cDate));
            });
        }

        return view('record.directory', [
            'records' => $records,
            'totalCount' => $totalCount,
            'speciesList' => FishBreed::orderBy('name')->get(['id', 'name']),
            'lakesList' => Lake::orderBy('name')->get(['id', 'name']),
            'anglersList' => Angler::orderBy('lastName')->get(['id', 'firstName', 'lastName']),
        ]);
    }

    /**
     * Show touch-optimized quick catch form for boat logging.
     *
     * @return \Illuminate\View\View
     */
    public function quick()
    {
        return view('record.quick', [
            'anglers' => Angler::orderBy('lastName', 'asc')->get(),
            'lakes' => Lake::orderBy('name', 'asc')->get(),
            'fishBreeds' => FishBreed::orderBy('name', 'asc')->get(),
            'lures' => Lure::orderBy('name', 'asc')->get(),
        ]);
    }

    /**
     * Show offline catch sync review page.
     *
     * @return \Illuminate\View\View
     */
    public function offlineReview()
    {
        $recentCatches = Record::with(['angler', 'lake', 'fishBreed', 'lure'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        return view('record.offline-review', [
            'recentCatches' => $recentCatches,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $record = Record::find($request->record);

        if ($record == null) {
            $record = new Record;
        }

        return view('record.create', [
            'record' => $record,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\StoreRecordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRecordRequest $request, CreateCatchRecordAction $createRecordAction, ProcessPhotoUploadAction $photoUploadAction)
    {
        $record = $createRecordAction->execute($request->validated());

        // Check if this catch achieves a Personal Best or Trophy milestone
        try {
            $milestone = $record->checkTrophyMilestone();
            if ($milestone) {
                $recipient = auth()->user() ?? $record->angler?->user;
                if ($recipient) {
                    $recipient->notify(new \Fishinglog\Notifications\TrophyCatchLogged($record, $milestone));
                }
                session()->flash('trophy_celebration', array_merge($milestone, [
                    'record_id' => $record->id,
                    'species_name' => $record->fishBreed ? $record->fishBreed->name : 'Fish',
                    'length' => $record->length,
                    'lake_name' => $record->lake ? $record->lake->name : 'Waterbody',
                ]));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to evaluate trophy milestone: ' . $e->getMessage());
        }

        // Handle optional uploaded photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $file) {
                $photoUploadAction->execute(
                    target: $record,
                    file: $file,
                    folder: 'photos/records',
                    isCover: ($index === 0)
                );
            }
        }

        return redirect()->action(
            [self::class, 'create'],
            ['record' => $record->id]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\View\View
     */
    public function show(Record $record)
    {
        $record->load(['angler', 'lake.dailyWeather', 'fishBreed', 'lure', 'photos']);

        return view('record.show', [
            'record' => $record,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\View\View
     */
    public function edit(Record $record)
    {
        $record->load('photos');

        return view('record.edit', [
            'record' => $record,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Fishinglog\Http\Requests\UpdateRecordRequest  $request
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRecordRequest $request, Record $record)
    {
        $targetRecord = Record::find($request->id) ?? $record;

        $targetRecord->anglers_id = $request->anglers_id;
        $targetRecord->lakes_id = $request->lakes_id;
        $targetRecord->fish_breeds_id = $request->fish_breeds_id;
        $targetRecord->lures_id = $request->lures_id;
        $targetRecord->weight = $request->weight;
        $targetRecord->length = $request->length;
        $targetRecord->temperature = $request->temperature;
        $targetRecord->latitude = $request->latitude;
        $targetRecord->longitude = $request->longitude;
        $targetRecord->released = $request->released;
        $targetRecord->caught = $request->caught;

        $targetRecord->save();

        // Handle optional uploaded photos
        if ($request->hasFile('photos')) {
            $hasExistingPhotos = $targetRecord->photos()->count() > 0;
            foreach ($request->file('photos') as $index => $file) {
                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = Str::uuid() . '.' . $extension;
                $path = $file->storeAs('photos/records', $filename, 'public');

                Photo::create([
                    'photoable_type' => Record::class,
                    'photoable_id' => $targetRecord->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'is_cover' => (!$hasExistingPhotos && $index === 0),
                    'user_id' => auth()->id(),
                    'sync_status' => 'pending_upstream',
                ]);
            }
        }

        return redirect('/record/' . $targetRecord->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Fishinglog\Models\Record  $record
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Record $record)
    {
        $record->delete();

        return redirect('/record')->with('status', 'Catch record removed successfully.');
    }
}
