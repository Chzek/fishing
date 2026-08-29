<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RecordControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $record;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->record = Record::factory()->create();
    }

    #[Test]
    public function unauthenticated_user_cannot_access_records()
    {
        $response = $this->get('/record');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_user_can_view_records_index()
    {
        $this->actingAs($this->user);

        $response = $this->get('/record');
        $response->assertStatus(200);
    }

    #[Test]
    public function authenticated_user_can_view_records_directory()
    {
        $this->actingAs($this->user);

        $response = $this->get('/record/directory');
        $response->assertStatus(200);
        $response->assertSee('Catches Logbook Directory');
    }

    #[Test]
    public function authenticated_user_can_create_a_record()
    {
        $this->actingAs($this->user);

        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();
        $lure = Lure::factory()->create();

        $response = $this->post('/record', [
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'lures_id' => $lure->id,
            'weight' => 5.25,
            'length' => 20.5,
            'temperature' => 72,
            'released' => 1,
            'caught' => '2026-08-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('records', [
            'anglers_id' => $angler->id,
            'length' => 20.5,
        ]);
    }

    #[Test]
    public function authenticated_user_can_view_a_record()
    {
        $this->actingAs($this->user);

        $response = $this->get('/record/' . $this->record->id);
        $response->assertStatus(200);
        $response->assertSee($this->record->fishBreed->name);
    }

    #[Test]
    public function authenticated_user_can_view_a_record_via_plural_records_route()
    {
        $this->actingAs($this->user);

        $response = $this->get('/records/' . $this->record->id);
        $response->assertStatus(200);
        $response->assertSee($this->record->fishBreed->name);
    }

    #[Test]
    public function authenticated_user_can_update_a_record()
    {
        $this->actingAs($this->user);

        $response = $this->put('/record', [
            'id' => $this->record->id,
            'anglers_id' => $this->record->anglers_id,
            'lakes_id' => $this->record->lakes_id,
            'fish_breeds_id' => $this->record->fish_breeds_id,
            'lures_id' => $this->record->lures_id,
            'weight' => 8.50,
            'length' => 24.0,
            'temperature' => 68,
            'released' => 0,
            'caught' => '2026-08-01',
        ]);

        $response->assertRedirect('/record/' . $this->record->id);
        $this->assertDatabaseHas('records', [
            'id' => $this->record->id,
            'length' => 24.0,
        ]);
    }
}
