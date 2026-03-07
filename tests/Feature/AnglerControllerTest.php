<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Fishinglog\Angler;
use Fishinglog\User;

class AnglerControllerTest extends TestCase
{
    use DatabaseMigrations; 

    protected $angler;
    protected $user;

    public function setUp() :void
    {
        parent::setUp();
        $this->angler = Angler::factory()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_create_an_angler()
    {
        $this->be($this->user);

        $this->post('/angler', $this->angler->toArray());
        $this->assertDatabaseHas('anglers', $this->angler->toArray());
    }

    /** @test */
    public function it_can_view_all_anglers()
    {
        $this->be($this->user);

        $response = $this->get('/angler');
        $response->assertSee($this->angler->lastName);
    }

    /** @test */
    public function it_can_view_an_angler()
    {
        $this->be($this->user);

        $response = $this->get('/angler/'.$this->angler->id);
        $response->assertSee($this->angler->lastName);
    }

    /** @test */
    public function it_can_update_an_angler_without_avatar()
    {
        $this->be($this->user);

        $response = $this->put('/angler', [
            'id' => $this->angler->id,
            'firstName' => 'Updated',
            'middleName' => $this->angler->middleName,
            'lastName' => $this->angler->lastName,
            'user_id' => $this->angler->user_id,
            'birthdate' => $this->angler->birthdate,
        ]);

        $response->assertRedirect('/angler/'.$this->angler->id);

        $this->assertDatabaseHas('anglers', [
            'id' => $this->angler->id,
            'firstName' => 'Updated',
        ]);
    }
}
