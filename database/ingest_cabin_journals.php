<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Fishinglog\Models\Angler;
use Fishinglog\Models\JournalEntry;
use Fishinglog\Models\JournalPage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$dryRun = in_array('--dry-run', $argv);

echo "=========================================================\n";
echo "  CANADIAN CABIN JOURNAL INGESTION " . ($dryRun ? "[DRY RUN]" : "[LIVE]") . "\n";
echo "=========================================================\n";

$entries = require __DIR__ . '/journal_entries_data.php';

echo "Loaded " . count($entries) . " journal entry specifications.\n";

// Pre-load anglers
$allAnglers = Angler::all();
$anglerMap = [];
foreach ($allAnglers as $angler) {
    $firstLast = trim($angler->firstName . ' ' . $angler->lastName);
    $anglerMap[strtolower($firstLast)] = $angler->id;
    if (!empty($angler->middleName)) {
        $firstMiddleLast = trim($angler->firstName . ' ' . $angler->middleName . ' ' . $angler->lastName);
        $anglerMap[strtolower($firstMiddleLast)] = $angler->id;
    }
}

$aliases = [
    'dave brauer' => 'David Brauer',
    'dave' => 'David Brauer',
    'david brauer' => 'David Brauer',
    'dan brauer' => 'Danny Brauer',
    'dan' => 'Danny Brauer',
    'danny' => 'Danny Brauer',
    'danny brauer' => 'Danny Brauer',
    'joe mroczek' => 'Joseph Perry Mroczek',
    'joe' => 'Joseph Perry Mroczek',
    'joseph mroczek' => 'Joseph Perry Mroczek',
    'joseph p. mroczek' => 'Joseph Perry Mroczek',
    'shirley mroczek' => 'Shirley Jean Mroczek',
    'shirley j. mroczek' => 'Shirley Jean Mroczek',
    'geren mroczek' => 'Geren Pierce Mroczek',
    'geren p. mroczek' => 'Geren Pierce Mroczek',
    'austin mroczek' => 'Austin Joseph Mroczek',
    'austin j. mroczek' => 'Austin Joseph Mroczek',
    'laura mroczek' => 'Laura Lee Mroczek',
    'laura l. mroczek' => 'Laura Lee Mroczek',
    'stanley mroczek' => 'Stanley Patryk Mroczek',
    'stanley p. mroczek' => 'Stanley Patryk Mroczek',
    'eve mroczek' => 'Evelyn Quinn Mroczek',
    'evelyn mroczek' => 'Evelyn Quinn Mroczek',
    'nick mroczek' => 'Nicholas Ryan Mroczek',
    'nicholas mroczek' => 'Nicholas Ryan Mroczek',
    'nicholas r. mroczek' => 'Nicholas Ryan Mroczek',
    'james deboer' => 'Richard James deBoer',
    'james de boer' => 'Richard James deBoer',
    'richard deboer' => 'Richard James deBoer',
    'richard j. deboer' => 'Richard James deBoer',
    'carrie van heukelum' => 'Carrie Fern Van Heukelum',
    'carrie f. van heukelum' => 'Carrie Fern Van Heukelum',
    'carrie' => 'Carrie Fern Van Heukelum',
    'travis van heukelum' => 'Travis Van Heukelum',
    'travis' => 'Travis Van Heukelum',
    'karen brauer' => 'Karen Brauer',
    'karen' => 'Karen Brauer',
    'andy brauer' => 'Andy Brauer',
    'andy' => 'Andy Brauer',
    'kendal brauer' => 'Kendal Brauer',
    'kendal' => 'Kendal Brauer',
    'matt gerring' => 'Matt Gerring',
    'matt' => 'Matt Gerring',
    'katy gerring' => 'Katy E Gerring',
    'katy' => 'Katy E Gerring',
    'sam gerring' => 'Sam Gerring',
    'sam' => 'Sam Gerring',
    'charlie brauer' => 'Charlie Brauer',
    'charlie' => 'Charlie Brauer',
    'amy brauer' => 'Amy Brauer',
    'amy' => 'Amy Brauer',
    'percy brauer' => 'Percy Vincent Brauer',
    'percy v. brauer' => 'Percy Vincent Brauer',
    'percy' => 'Percy Vincent Brauer',
    'simon brauer' => 'Simon Brauer',
    'simon' => 'Simon Brauer',
    'penelope brauer' => 'Penelope Brauer',
    'bryon linfield' => 'Bryon Linfield',
    'chrissy linfield' => 'Chrissy Linfield',
    'todd whitaker' => 'Todd Whitaker',
    'chrissy whitaker' => 'Chrissy Whitaker',
    'eric whitaker' => 'Eric Whitaker',
    'red brauer' => 'Red (Grandma) Brauer',
    'red (grandma) brauer' => 'Red (Grandma) Brauer',
    'grandma brauer' => 'Red (Grandma) Brauer',
    'steve r' => 'Steve R',
    'steve reith' => 'Steve R',
    'jackson palmer' => 'Jackson Palmer',
    'isaac taylor' => 'Isaac Taylor',
    'grandpa brauer' => 'Grandpa Brauer',
];

function resolveAnglerId(string $name, array $anglerMap, array $aliases): ?string {
    $normalized = strtolower(trim($name));
    if (isset($aliases[$normalized])) {
        $normalized = strtolower($aliases[$normalized]);
    }
    return $anglerMap[$normalized] ?? null;
}

// Track mapped pages
$allMappedPages = [];

DB::beginTransaction();

try {
    $entryNum = 5;
    $createdCount = 0;
    $pagesUpdatedCount = 0;
    $anglersAttachedCount = 0;

    foreach ($entries as $entryData) {
        $title = $entryData['title'];
        $entryDate = $entryData['entry_date'];
        $startDate = $entryData['start_date'];
        $endDate = $entryData['end_date'];
        $bodyMarkdown = $entryData['body_markdown'];
        $highlights = $entryData['highlights'] ?? null;
        $weatherSummary = $entryData['weather_summary'] ?? null;
        $locationSummary = $entryData['location_summary'] ?? null;
        $pages = $entryData['pages'] ?? [];
        $anglers = $entryData['anglers'] ?? [];

        if (!$dryRun) {
            $entry = JournalEntry::create([
                'id' => (string) Str::uuid(),
                'expeditions_id' => null, // Explicitly NULL per requirement
                'entry_date' => $entryDate,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'title' => $title,
                'body_markdown' => $bodyMarkdown,
                'highlights' => $highlights,
                'weather_summary' => $weatherSummary,
                'location_summary' => $locationSummary,
                'template_type' => 'cabin_journal',
                'notes' => null,
            ]);
            $createdCount++;

            // Link pages
            foreach ($pages as $idx => $filename) {
                $allMappedPages[] = $filename;
                $page = JournalPage::where('filename', $filename)->first();
                if ($page) {
                    $page->update([
                        'journal_entry_id' => $entry->id,
                        'page_number' => $idx + 1,
                        'is_processed' => true,
                    ]);
                    $pagesUpdatedCount++;
                } else {
                    echo "WARNING: JournalPage not found for filename: {$filename} (Entry: {$title})\n";
                }
            }

            // Link anglers
            foreach ($anglers as $anglerName) {
                $anglerId = resolveAnglerId($anglerName, $anglerMap, $aliases);
                if ($anglerId) {
                    $entry->anglers()->syncWithoutDetaching([$anglerId]);
                    $anglersAttachedCount++;
                } else {
                    echo "NOTE: Unregistered angler '{$anglerName}' not linked in Entry: {$title}\n";
                }
            }
        } else {
            foreach ($pages as $filename) {
                $allMappedPages[] = $filename;
            }
            foreach ($anglers as $anglerName) {
                $anglerId = resolveAnglerId($anglerName, $anglerMap, $aliases);
                if (!$anglerId) {
                    echo "DRY-RUN NOTE: Angler '{$anglerName}' not in DB map.\n";
                }
            }
        }

        $entryNum++;
    }

    if ($dryRun) {
        DB::rollBack();
        echo "\n[DRY RUN COMPLETE] Verified " . count($entries) . " entries.\n";
        echo "Mapped pages count across entries 5-108: " . count($allMappedPages) . "\n";
    } else {
        DB::commit();
        echo "\n[SUCCESS] Live Ingestion Complete!\n";
        echo "Created Journal Entries: {$createdCount}\n";
        echo "Updated Journal Pages: {$pagesUpdatedCount}\n";
        echo "Attached Angler Links: {$anglersAttachedCount}\n";
    }

} catch (\Throwable $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
