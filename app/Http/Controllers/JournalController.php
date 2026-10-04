<?php

namespace Fishinglog\Http\Controllers;

use Fishinglog\Models\JournalEntry;
use Fishinglog\Models\JournalPage;
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
    public function index(Request $request): View
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
        $totalPagesCount = JournalPage::count();
        $totalEntriesCount = JournalEntry::count();

        $availableYears = JournalEntry::whereNotNull('entry_date')
            ->selectRaw('DISTINCT YEAR(entry_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('journal.index', [
            'entries' => $entries,
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
}

