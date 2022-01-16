<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Fishinglog\Lake;

class LakeTest extends TestCase
{
    use DatabaseMigrations;

    protected $lake;

    public function setUp() :void
    {
        parent::setUp();

        $this->lake = Lake::factory()->create();
    }

    /** @test */
    public function it_can_create_a_lake()
    {
        $this->assertDatabaseHas('lakes', ['id' => $this->lake->id]);
    }
}
