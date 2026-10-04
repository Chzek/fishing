<?php

namespace Fishinglog\Services;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\JournalEntry;
use Fishinglog\Models\JournalPage;
use Fishinglog\Models\Lake;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JournalTranscriptionService
{
    public function __construct(
        public ExpeditionDiscoveryService $expeditionDiscovery
    ) {
    }

    /**
     * Parse and import a structured journal page or batch payload.
     *
     * @param array<string, mixed> $payload
     * @return JournalEntry
     */
    public function importParsedEntry(array $payload): JournalEntry
    {
        $entryDate = !empty($payload['entry_date']) ? Carbon::parse($payload['entry_date']) : null;
        $startDate = !empty($payload['start_date']) ? Carbon::parse($payload['start_date']) : $entryDate;
        $endDate = !empty($payload['end_date']) ? Carbon::parse($payload['end_date']) : $entryDate;

        // 1. Check for existing expedition match
        $expedition = !empty($payload['expeditions_id'])
            ? Expedition::find($payload['expeditions_id'])
            : $this->expeditionDiscovery->findExpeditionForDate($entryDate ?? $startDate);

        // 2. Create JournalEntry
        /** @var JournalEntry $entry */
        $entry = JournalEntry::create([
            'expeditions_id' => $expedition?->id,
            'entry_date' => $entryDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'title' => $payload['title'] ?? ($entryDate ? "Journal: " . $entryDate->format('M j, Y') : 'Expedition Logbook Entry'),
            'body_markdown' => $payload['body_markdown'] ?? '',
            'highlights' => $payload['highlights'] ?? null,
            'weather_summary' => $payload['weather_summary'] ?? null,
            'location_summary' => $payload['location_summary'] ?? null,
            'template_type' => $payload['template_type'] ?? 'cabin_journal',
            'notes' => $payload['notes'] ?? null,
        ]);

        // 3. Attach JournalPages if provided
        if (!empty($payload['pages']) && is_array($payload['pages'])) {
            foreach ($payload['pages'] as $index => $pageData) {
                JournalPage::create([
                    'journal_entry_id' => $entry->id,
                    'page_number' => $pageData['page_number'] ?? ($index + 1),
                    'sequence_order' => $pageData['sequence_order'] ?? ($index + 1),
                    'filename' => $pageData['filename'] ?? basename($pageData['photo_path'] ?? 'page.jpg'),
                    'photo_path' => $pageData['photo_path'] ?? '',
                    'raw_ocr_text' => $pageData['raw_ocr_text'] ?? null,
                    'structured_metadata' => $pageData['structured_metadata'] ?? null,
                    'is_processed' => true,
                ]);
            }
        }

        // 4. Tag mentioned Anglers & Lakes
        $this->syncMentionedEntities($entry, $payload['anglers'] ?? [], $payload['lakes'] ?? []);

        return $entry;
    }

    /**
     * Match and sync mentioned anglers and lakes by name.
     *
     * @param JournalEntry $entry
     * @param array<int, string|int> $anglerNames
     * @param array<int, string|int> $lakeNames
     * @return void
     */
    public function syncMentionedEntities(JournalEntry $entry, array $anglerNames, array $lakeNames): void
    {
        // Match Anglers (Exact full-name match only to prevent false historical tagging)
        $anglerIds = [];
        $allAnglers = Angler::all();

        foreach ($anglerNames as $rawName) {
            $nameStr = trim((string) $rawName);
            if ($nameStr === '') {
                continue;
            }

            $matched = $allAnglers->first(function (Angler $a) use ($nameStr) {
                $fullName = mb_strtolower(trim($a->firstName . ' ' . $a->lastName));
                return $fullName === mb_strtolower($nameStr);
            });

            if ($matched) {
                $anglerIds[] = $matched->id;
            }
        }

        if (!empty($anglerIds)) {
            $entry->anglers()->sync(array_unique($anglerIds));
        } else {
            $entry->anglers()->detach();
        }

        // Match Lakes (Exact lake name match only)
        $lakeIds = [];
        $allLakes = Lake::all();

        foreach ($lakeNames as $rawLake) {
            $lakeStr = trim((string) $rawLake);
            if ($lakeStr === '') {
                continue;
            }

            $matched = $allLakes->first(function (Lake $l) use ($lakeStr) {
                return mb_strtolower(trim($l->name)) === mb_strtolower($lakeStr);
            });

            if ($matched) {
                $lakeIds[] = $matched->id;
            }
        }

        if (!empty($lakeIds)) {
            $entry->lakes()->sync(array_unique($lakeIds));
        } else {
            $entry->lakes()->detach();
        }
    }

    /**
     * Build standard sample parsed entry from known journal pages (e.g. 1327 & 1328).
     *
     * @return array<string, mixed>
     */
    public function getSample2007Payload(): array
    {
        return [
            'entry_date' => '2007-06-29',
            'start_date' => '2007-06-28',
            'end_date' => '2007-07-01',
            'title' => 'June 2007: Trip to Cabin & Rapids on Catfish Creek',
            'weather_summary' => '78°F, cloudy to partly sunny, cold & windy, scattered showers',
            'location_summary' => 'Catfish Creek, Princess Creek, Nick Lake, Alabama Lake',
            'highlights' => 'Andy & James ran the Catfish Creek rapids and flipped the canoe, busting both paddles and losing Andy\'s new rod and a boat cushion. Dave fried fresh fish. Joe & James froze brook trout with Joe\'s FoodSaver.',
            'body_markdown' => <<<MD
### June 28, 2007
* **Guests / Crew:** Red Brauer, Dave, Joe Mroczek, Andy, James deBoer and I.
* **Weather:** 78°F and cloudy to partly sunny.
* **What We Did:** Red and I drove from Jackson to Canada. We left Jackson at 12:00 p.m. and arrived at the cabin at 8:40 p.m. We made really good time. First time I drove the whole way. Dave fried fish for Red and I. Joe, James & Andy went brook trout fishing, caught several.

### June 29, 2007
* **Highlights:** We took Grandma out in Joe's motor boat around the big lake. It was windy and cold.
* **Catfish Creek Rapids:** Andy & James went down Catfish Creek. They left at 11:00 AM and landed at 5:30. They had a bumpy ride, hit several rapids, and flipped the canoe several times! They busted both paddles, lost Andy's new fishing pole and a boat cushion.
* **Evening:** Dave & Joe fished Princess Creek from 4:30 p.m. to 10:30 p.m. Freezing fish with Joe's FoodSaver.

### June 30, 2007
* **Garage Sales & Fishing:** Saturday Red, James, Andy, Dave & I went to town checking out garage sales (nothing good).
* Joe went Brook Trout fishing (no luck). All the guys went fishing at Alabama and No Name Lake, catching 9 fish. Red & I hung out at the cabin. Weather was cool, partly cloudy with scattered showers.

### July 1, 2007
* Joe & James left this morning. Joe went fishing on Catfish early. Weather was nice, sunny and a little breezy, mid 70s.
* Dave, Andy & I went to Nick Lake for Brook Trout. Andy did well—caught 5, threw one back. I caught 1.
* Dave, Andy & Grandma watched *Anatomy of a Murder*. Chrissy & Bryan stopped in on a quick trip.
MD,
            'anglers' => ['Joe Mroczek', 'Dave', 'Andy', 'Red Brauer', 'James deBoer'],
            'lakes' => ['Catfish Creek', 'Princess Creek', 'Nick Lake', 'Alabama Lake'],
            'pages' => [
                [
                    'page_number' => 1,
                    'sequence_order' => 1,
                    'filename' => '1327.jpg',
                    'photo_path' => 'journals/raw/1327.jpg',
                ],
                [
                    'page_number' => 2,
                    'sequence_order' => 2,
                    'filename' => '1328.jpg',
                    'photo_path' => 'journals/raw/1328.jpg',
                ],
            ],
        ];
    }
}
