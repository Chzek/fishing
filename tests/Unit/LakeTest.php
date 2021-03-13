<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Fishinglog\Angler;

class LakeTest extends TestCase
{
    use DatabaseMigrations;

    protected $lake;

    public function setUp() :void
    {
        parent::setUp();

        $this->lake = factory(\Fishinglog\Lake::class)->create();
    }

    /** @test */
    public function it_can_create_a_lake()
    {
        $this->assertDatabaseHas('lakes', $this->lake->toArray());
    }
}
