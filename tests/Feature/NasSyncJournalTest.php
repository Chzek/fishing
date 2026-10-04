<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\JournalEntry;
use Fishinglog\Models\JournalPage;
use Fishinglog\Models\Lake;
use Fishinglog\Models\User;
use Fishinglog\Services\NasSyncService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NasSyncJournalTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[Test]
    public function it_orchestrates_journal_entry_and_page_sync_via_nas_sync_service()
    {
        Storage::fake('public');

        $angler = Angler::factory()->create([
            'firstName' => 'Garry',
            'lastName' => 'M',
        ]);
        $lake = Lake::factory()->create(['name' => 'Bark Lake']);

        // Create local journal entry with pivot relations
        $entry = JournalEntry::create([
            'entry_date' => '1995-07-15',
            'title' => 'Trip to Bark Lake 1995',
            'body_markdown' => 'Caught several largemouth bass.',
            'weather_summary' => 'Sunny and calm',
        ]);
        $entry->anglers()->sync([$angler->id]);
        $entry->lakes()->sync([$lake->id]);

        // Create local journal page with media file
        $pageFilePath = 'journals/raw/1001.jpg';
        Storage::disk('public')->put($pageFilePath, 'fake-binary-journal-scan-content');
        $page = JournalPage::create([
            'journal_entry_id' => $entry->id,
            'page_number' => 1,
            'sequence_order' => 1,
            'filename' => '1001.jpg',
            'photo_path' => $pageFilePath,
            'is_processed' => true,
        ]);

        $remoteEntryUuid = '99999999-1111-2222-3333-444444444444';
        $remotePageUuid = '88888888-1111-2222-3333-444444444444';
        $remotePageFile = 'journals/raw/1002.jpg';
        $remoteScanData = 'nas-scanned-page-binary-data';

        $angler->update(['sync_status' => 'synced']);
        $lake->update(['sync_status' => 'synced']);

        Http::fake([
            'https://nas.example.com/api/v1/sync/media/batch-verify' => Http::response([
                'status' => 'success',
                'statuses' => [
                    $pageFilePath => 'missing',
                ],
            ], 200),
            'https://nas.example.com/api/v1/sync/media/chunk' => Http::response([
                'status' => 'success',
            ], 200),
            'https://nas.example.com/api/v1/sync/push' => function (\Illuminate\Http\Client\Request $request) use ($entry, $page) {
                $data = $request->data();

                if (isset($data['journal_entries'])) {
                    $entries = $data['journal_entries'];
                    $this->assertEquals($entry->id, $entries[0]['id'] ?? $entries[0]['uuid']);
                    $this->assertContains($entry->anglers->first()->id, $entries[0]['angler_ids'] ?? []);
                    $this->assertContains($entry->lakes->first()->id, $entries[0]['lake_ids'] ?? []);

                    return Http::response([
                        'status' => 'success',
                        'synced_uuids' => [$entry->id],
                        'processed_count' => count($entries),
                    ], 200);
                }

                if (isset($data['journal_pages'])) {
                    $pages = $data['journal_pages'];
                    $this->assertEquals($page->id, $pages[0]['id'] ?? $pages[0]['uuid']);

                    return Http::response([
                        'status' => 'success',
                        'synced_uuids' => [$page->id],
                        'processed_count' => count($pages),
                    ], 200);
                }

                return Http::response([
                    'status' => 'success',
                    'synced_uuids' => [],
                    'processed_count' => 0,
                ], 200);
            },
            'https://nas.example.com/api/v1/sync/pull*' => Http::response([
                'journal_entries' => [
                    [
                        'id' => $remoteEntryUuid,
                        'entry_date' => '1996-08-10',
                        'title' => 'Remote Cabin Entry',
                        'body_markdown' => 'Great pike fishing day.',
                        'weather_summary' => 'Overcast',
                        'angler_ids' => [$angler->id],
                        'lake_ids' => [$lake->id],
                        'updated_at' => '2026-08-09T10:00:00Z',
                    ]
                ],
                'journal_pages' => [
                    [
                        'id' => $remotePageUuid,
                        'journal_entry_id' => $remoteEntryUuid,
                        'page_number' => 1,
                        'sequence_order' => 1,
                        'filename' => '1002.jpg',
                        'photo_path' => $remotePageFile,
                        'file_base64' => base64_encode($remoteScanData),
                        'updated_at' => '2026-08-09T10:00:00Z',
                    ]
                ],
                'server_timestamp' => '2026-08-09T15:00:00Z',
            ], 200),
        ]);

        $service = new NasSyncService('https://nas.example.com', 'test-admin-token');
        $result = $service->sync();

        $this->assertGreaterThanOrEqual(1, $result['pushed']);
        $this->assertGreaterThanOrEqual(1, $result['pulled']);

        // Verify local entries marked synced
        $this->assertEquals('synced', $entry->fresh()->sync_status);
        $this->assertEquals('synced', $page->fresh()->sync_status);

        // Verify remote entry pulled into DB
        $this->assertDatabaseHas('journal_entries', [
            'id' => $remoteEntryUuid,
            'title' => 'Remote Cabin Entry',
            'sync_status' => 'synced',
        ]);

        // Verify remote entry pivot relationships
        $pulledEntry = JournalEntry::find($remoteEntryUuid);
        $this->assertTrue($pulledEntry->anglers->contains($angler->id));
        $this->assertTrue($pulledEntry->lakes->contains($lake->id));

        // Verify remote page pulled and binary written to disk
        $this->assertDatabaseHas('journal_pages', [
            'id' => $remotePageUuid,
            'journal_entry_id' => $remoteEntryUuid,
            'photo_path' => $remotePageFile,
            'sync_status' => 'synced',
        ]);
        $this->assertTrue(Storage::disk('public')->exists($remotePageFile));
        $this->assertEquals($remoteScanData, Storage::disk('public')->get($remotePageFile));
    }

    #[Test]
    public function admin_can_push_and_pull_journal_models_via_api()
    {
        Storage::fake('public');

        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();

        $entryUuid = '77777777-1111-2222-3333-444444444444';
        $pageUuid = '66666666-1111-2222-3333-444444444444';
        $photoPath = 'journals/raw/9999.jpg';
        $fileContent = 'test-scan-raw-content';

        // 1. Push journal entry & page with base64 file
        $pushPayload = [
            'journal_entries' => [
                [
                    'uuid' => $entryUuid,
                    'entry_date' => '1998-06-20',
                    'title' => 'Api Pushed Journal',
                    'body_markdown' => 'Api test body markdown',
                    'angler_ids' => [$angler->id],
                    'lake_ids' => [$lake->id],
                    'updated_at' => '2026-08-09T12:00:00Z',
                ]
            ],
            'journal_pages' => [
                [
                    'uuid' => $pageUuid,
                    'journal_entry_id' => $entryUuid,
                    'page_number' => 1,
                    'sequence_order' => 1,
                    'filename' => '9999.jpg',
                    'photo_path' => $photoPath,
                    'file_base64' => base64_encode($fileContent),
                    'updated_at' => '2026-08-09T12:00:00Z',
                ]
            ]
        ];

        $response = $this->actingAs($admin)->postJson('/api/v1/sync/push', $pushPayload);
        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('journal_entries', [
            'id' => $entryUuid,
            'title' => 'Api Pushed Journal',
            'sync_status' => 'synced',
        ]);

        $savedEntry = JournalEntry::find($entryUuid);
        $this->assertTrue($savedEntry->anglers->contains($angler->id));
        $this->assertTrue($savedEntry->lakes->contains($lake->id));

        $this->assertDatabaseHas('journal_pages', [
            'id' => $pageUuid,
            'journal_entry_id' => $entryUuid,
            'sync_status' => 'synced',
        ]);
        $this->assertTrue(Storage::disk('public')->exists($photoPath));

        // 2. Pull journal models
        $pullResponse = $this->actingAs($admin)->getJson('/api/v1/sync/pull?since=2026-01-01T00:00:00Z');
        $pullResponse->assertStatus(200);
        $pullResponse->assertJsonStructure(['journal_entries', 'journal_pages']);

        $pulledEntries = collect($pullResponse->json('journal_entries'));
        $this->assertTrue($pulledEntries->contains('id', $entryUuid));

        $pulledPages = collect($pullResponse->json('journal_pages'));
        $this->assertTrue($pulledPages->contains('id', $pageUuid));
        $pulledPage = $pulledPages->firstWhere('id', $pageUuid);
        $this->assertNotEmpty($pulledPage['file_base64']);
    }
}
