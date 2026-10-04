<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\Expedition;
use Fishinglog\Models\JournalEntry;
use Fishinglog\Models\JournalPage;
use Fishinglog\Services\ExpeditionDiscoveryService;
use Fishinglog\Services\JournalTranscriptionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of digitized expedition journals.
     */
    public function index(Request $request, ExpeditionDiscoveryService $discoveryService): View
    {
        $query = JournalEntry::with(['expedition', 'pages', 'anglers', 'lakes'])
            ->orderBy('entry_date', 'desc');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body_markdown', 'like', "%{$search}%")
                    ->orWhere('highlights', 'like', "%{$search}%")
                    ->orWhere('weather_summary', 'like', "%{$search}%")
                    ->orWhere('location_summary', 'like', "%{$search}%");
            });
        }

        if ($year = $request->input('year')) {
            $query->whereYear('entry_date', $year);
        }

        $entries = $query->paginate(12)->withQueryString();
        $recommendedExpeditions = $discoveryService->getRecommendedExpeditions();
        $totalPagesCount = JournalPage::count();
        $totalEntriesCount = JournalEntry::count();

        $availableYears = JournalEntry::whereNotNull('entry_date')
            ->selectRaw('DISTINCT YEAR(entry_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('journal.index', [
            'entries' => $entries,
            'recommendedExpeditions' => $recommendedExpeditions,
            'totalPagesCount' => $totalPagesCount,
            'totalEntriesCount' => $totalEntriesCount,
            'availableYears' => $availableYears,
            'selectedYear' => $year,
            'search' => $search,
        ]);
    }

    /**
     * Display a single journal entry with its high-res scan pages and metadata.
     */
    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load(['expedition', 'pages', 'anglers', 'lakes']);

        return view('journal.show', [
            'entry' => $journalEntry,
        ]);
    }

    /**
     * Create an expedition from a discovery recommendation.
     */
    public function acceptRecommendation(Request $request, ExpeditionDiscoveryService $discoveryService)
    {
        $validated = $request->validate([
            'suggested_title' => 'required|string',
            'start_date' => 'required|date',
            'finish_date' => 'required|date',
            'entry_ids' => 'array',
            'entry_ids.*' => 'string',
        ]);

        $recommendations = $discoveryService->getRecommendedExpeditions();
        $target = $recommendations->first(function ($rec) use ($validated) {
            return $rec['start_date'] === $validated['start_date'] && $rec['finish_date'] === $validated['finish_date'];
        });

        if ($target) {
            $expedition = $discoveryService->createFromRecommendation($target);
            return redirect('/expedition/' . $expedition->id)->with('status', "Expedition '{$expedition->title}' created successfully from journal recommendation.");
        }

        return redirect()->back()->with('error', 'Recommended expedition could not be found or has already been created.');
    }
}
