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
