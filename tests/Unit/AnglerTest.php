<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Fishinglog\Angler;

class AnglerTest extends TestCase
{
    use DatabaseMigrations;

    protected $angler;

    public function setUp() :void
    {
        parent::setUp();

        $this->angler = factory('Fishinglog\Angler')->create();             
    }

    /** @test */
    public function it_cannot_create_a_duplicate_angler()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $angler_copy = $this->angler->replicate();
        $angler_copy->save();
    }
}
