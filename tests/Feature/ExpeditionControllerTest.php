<?php

namespace Tests\Feature;

use Fishinglog\Models\Expedition;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ExpeditionControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function unauthenticated_user_cannot_access_expeditions()
    {
        $response = $this->get('/expedition');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_view_expeditions_index()
    {
        $this->actingAs($this->user);

        $response = $this->get('/expedition');
        $response->assertStatus(200);
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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
}
