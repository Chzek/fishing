<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Expedition;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExpeditionControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function unauthenticated_user_cannot_access_expeditions()
    {
        $response = $this->get('/expedition');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_user_can_view_expeditions_index()
    {
        $this->actingAs($this->user);

        $response = $this->get('/expedition');
        $response->assertStatus(200);
    }

    #[Test]
    public function authenticated_user_can_create_an_expedition()
    {
        $this->actingAs($this->user);

        $response = $this->post('/expedition', [
            'description' => 'Summer Bass Tournament',
            'start' => '2026-08-01',
            'finish' => '2026-08-05',
        ]);

        $response->assertRedirect('/expedition');
        $this->assertDatabaseHas('expeditions', [
            'description' => 'Summer Bass Tournament',
        ]);
    }

    #[Test]
    public function authenticated_user_can_view_an_expedition()
    {
        $this->actingAs($this->user);

        $expedition = new Expedition();
        $expedition->description = 'Boundary Waters Trip';
        $expedition->start = '2026-08-01';
        $expedition->finish = '2026-08-10';
        $expedition->save();

        $response = $this->get('/expedition/' . $expedition->id);
        $response->assertStatus(200);
        $response->assertSee('Boundary Waters Trip');
    }

    #[Test]
    public function authenticated_user_can_view_expedition_with_normalized_best_catches_strip()
    {
        $this->actingAs($this->user);

        $expedition = Expedition::create([
            'description' => 'Georgian Bay Expedition 2026',
            'start' => '2026-08-01',
            'finish' => '2026-08-10',
        ]);

        $angler = \Fishinglog\Models\Angler::factory()->create(['firstName' => 'Marcus', 'middleName' => '', 'lastName' => 'Flynn']);
        $lake = \Fishinglog\Models\Lake::factory()->create(['name' => 'Bad River Channel']);
        $pike = \Fishinglog\Models\FishBreed::factory()->create(['name' => 'Northern Pike', 'trophy_length_bench' => 36.0]);
        $bass = \Fishinglog\Models\FishBreed::factory()->create(['name' => 'Smallmouth Bass', 'trophy_length_bench' => 20.0]);

        // 34" Pike = 34/36 = 94.4 pts
        \Fishinglog\Models\Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $pike->id,
            'length' => 34.0,
            'weight' => 11.20,
            'caught' => '2026-08-03',
        ]);

        // 21" Bass = 21/20 = 105.0 pts (Should be #1 over the 34" Pike!)
        \Fishinglog\Models\Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $bass->id,
            'length' => 21.0,
            'weight' => 5.40,
            'caught' => '2026-08-05',
        ]);

        $response = $this->get('/expedition/' . $expedition->id);
        $response->assertStatus(200);
        $response->assertSee('Expedition Best Catches');
        $response->assertSee('Species Normalized');
        $response->assertSee('105.0 pts');
        $response->assertSee('94.4 pts');
        $response->assertSee('Smallmouth Bass • Bad River Channel');
    }

    #[Test]
    public function authenticated_user_can_update_an_expedition()
    {
        $this->actingAs($this->user);

        $expedition = new Expedition();
        $expedition->description = 'Initial Trip Description';
        $expedition->start = '2026-08-01';
        $expedition->finish = '2026-08-03';
        $expedition->save();

        $response = $this->put('/expedition', [
            'id' => $expedition->id,
            'description' => 'Updated Trip Description',
            'start' => '2026-08-01',
            'finish' => '2026-08-04',
        ]);

        $response->assertRedirect('/expedition/' . $expedition->id);
        $this->assertDatabaseHas('expeditions', [
            'id' => $expedition->id,
            'description' => 'Updated Trip Description',
        ]);
    }

    #[Test]
    public function authenticated_user_can_view_edit_expedition_with_populated_dates()
    {
        $this->actingAs($this->user);

        $expedition = Expedition::create([
            'description' => 'Guys Trip 2026',
            'start' => '2026-08-10',
            'finish' => '2026-08-16',
        ]);

        $response = $this->get('/expedition/' . $expedition->id . '/edit');
        $response->assertStatus(200);
        $response->assertSee('value="2026-08-10"', false);
        $response->assertSee('value="2026-08-16"', false);
        $response->assertSee('value="Guys Trip 2026"', false);
    }

    #[Test]
    public function authenticated_user_can_view_create_expedition_form()
    {
        $this->actingAs($this->user);

        $response = $this->get('/expedition/create');
        $response->assertStatus(200);
        $response->assertSee('Create Expedition Trip');
        $response->assertSee('Plan a wilderness trip');
    }
}
