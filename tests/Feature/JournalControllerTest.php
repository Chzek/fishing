<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\JournalEntry;
use Fishinglog\Models\JournalPage;
use Fishinglog\Models\Lake;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JournalControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function unauthenticated_user_cannot_access_journals(): void
    {
        $response = $this->get('/journal');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_user_can_view_journal_index(): void
    {
        $this->be($this->user);

        $entry = JournalEntry::factory()->create([
            'title' => 'Rapids on Catfish Creek',
            'highlights' => 'Flipped the canoe several times in the rapids',
            'weather_summary' => '78°F, cloudy to partly sunny',
            'entry_date' => '2007-06-29',
        ]);

        JournalPage::factory()->create([
            'journal_entry_id' => $entry->id,
            'filename' => '1327.jpg',
        ]);

        $response = $this->get('/journal');
        $response->assertStatus(200);
        $response->assertSee('Expedition Journals & Cabin Logs');
        $response->assertSee('Rapids on Catfish Creek');
        $response->assertSee('Flipped the canoe several times in the rapids');
        $response->assertSee('78°F, cloudy to partly sunny');
    }

    #[Test]
    public function journal_index_supports_search_and_year_filters(): void
    {
        $this->be($this->user);

        JournalEntry::factory()->create([
            'title' => 'Searchable Secret Hotspot',
            'entry_date' => '2007-07-01',
        ]);

        JournalEntry::factory()->create([
            'title' => 'Other Logbook Entry',
            'entry_date' => '2015-08-10',
        ]);

        $searchResponse = $this->get('/journal?search=Secret');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Searchable Secret Hotspot');
        $searchResponse->assertDontSee('Other Logbook Entry');

        $yearResponse = $this->get('/journal?year=2015');
        $yearResponse->assertStatus(200);
        $yearResponse->assertSee('Other Logbook Entry');
        $yearResponse->assertDontSee('Searchable Secret Hotspot');
    }

    #[Test]
    public function authenticated_user_can_view_journal_show(): void
    {
        $this->be($this->user);

        $angler = Angler::factory()->create(['firstName' => 'Arthur', 'lastName' => 'Dent']);
        $lake = Lake::factory()->create(['name' => 'Catfish Creek']);

        $entry = JournalEntry::factory()->create([
            'title' => 'June 2007 Cabin Journal',
            'body_markdown' => '### June 29, 2007\n\nDave fried fresh fish.',
            'highlights' => 'Dave fried fresh fish',
        ]);

        $entry->anglers()->attach($angler->id);
        $entry->lakes()->attach($lake->id);

        $page = JournalPage::factory()->create([
            'journal_entry_id' => $entry->id,
            'filename' => '1327.jpg',
            'sequence_order' => 1,
        ]);

        $response = $this->get('/journal/' . $entry->id);
        $response->assertStatus(200);
        $response->assertSee('June 2007 Cabin Journal');
        $response->assertSee('Dave fried fresh fish');
        $response->assertSee('Arthur Dent');
        $response->assertSee('Catfish Creek');
        $response->assertSee($page->url);
    }
}

