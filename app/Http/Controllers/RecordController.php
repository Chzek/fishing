<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Actions\Media\ProcessPhotoUploadAction;
use Fishinglog\Actions\Records\CreateCatchRecordAction;
use Fishinglog\Actions\Records\UpdateCatchRecordAction;
use Fishinglog\Http\Requests\StoreRecordRequest;
use Fishinglog\Http\Requests\UpdateRecordRequest;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Services\CatchTelemetryService;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

class RecordController extends Controller
{
    use Notifiable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, CatchTelemetryService $telemetryService)
    {
        if ($request->hasAny(['search', 'length', 'length_operator', 'angler', 'sort_by', 'sort_order'])) {
            return redirect()->route('record.directory', $request->query());
        }

        $telemetry = $telemetryService->getOrCalculateTelemetry();

        return view('record.index', $telemetry);
    }

    /**
     * Display the Catches Logbook Directory table with search and filters.
     * Table querying, filtering, sorting, pagination, and weather hydration
     * are encapsulated inside the reactive GenericDataTable Livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function directory()
    {
        return view('record.directory', [
            'totalCount' => Record::count(),
            'speciesList' => FishBreed::orderBy('name')->get(['id', 'name']),
            'lakesList' => Lake::orderBy('name')->get(['id', 'name']),
            'anglersList' => Angler::orderBy('lastName')->get(['id', 'firstName', 'middleName', 'lastName']),
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
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $record = null;
        if ($request->filled('record')) {
            $record = Record::find($request->record);
        }

        if ($record == null) {
            $record = new Record();
        }

        return view('record.create', [
            'record' => $record,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRecordRequest  $request
     * @param  CreateCatchRecordAction  $createRecordAction
     * @param  ProcessPhotoUploadAction  $photoUploadAction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRecordRequest $request, CreateCatchRecordAction $createRecordAction, ProcessPhotoUploadAction $photoUploadAction)
    {
        $record = $createRecordAction->execute($request->validated());

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
     * @param  Record  $record
     * @return \Illuminate\View\View
     */
    public function show(Record $record)
    {
        $record->load(['angler', 'lake', 'fishBreed.family', 'lure', 'photos']);
        Record::hydrateDailyWeatherForCollection(collect([$record]));

        return view('record.show', [
            'record' => $record,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Record  $record
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
     * @param  UpdateRecordRequest  $request
     * @param  Record  $record
     * @param  UpdateCatchRecordAction  $updateRecordAction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRecordRequest $request, Record $record, UpdateCatchRecordAction $updateRecordAction)
    {
        $targetRecord = Record::find($request->id) ?? $record;
        $uploadedPhotos = $request->hasFile('photos') ? (array) $request->file('photos') : [];

        $updatedRecord = $updateRecordAction->execute(
            record: $targetRecord,
            data: $request->validated(),
            uploadedPhotos: $uploadedPhotos
        );

        return redirect('/record/' . $updatedRecord->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Record  $record
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Record $record)
    {
        $record->delete();

        return redirect('/record')->with('status', 'Catch record removed successfully.');
    }
}
