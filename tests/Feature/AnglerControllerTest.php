<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnglerControllerTest extends TestCase
{
    use DatabaseMigrations; 

    protected $angler;

    public function setUp() :void
    {
        parent::setUp();
        $this->angler = factory(\Fishinglog\Angler::class)->create();
    }

    /** @test */
    public function it_can_view_all_anglers()
    {
        $response = $this->get('/angler');
        $response->assertSee($this->angler->lastName);

    }

    /** @test */
    public function it_can_view_an_angler()
    {
        $response = $this->get('/angler/'.$this->angler->id);
        $response->assertSee($this->angler->lastName);
    }

    /** @test */
    public function it_can_create_an_angler()
    {
        
        $this->post('/angler', compact($this->angler));

        $this->assertDatabaseHas('anglers', $this->angler->toArray());
    }
}
