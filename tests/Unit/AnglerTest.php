<?php

namespace Tests\Unit;

use Fishinglog\Models\Angler;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AnglerTest extends TestCase
{
    use DatabaseMigrations;

    protected $angler;

    public function setUp(): void
    {
        parent::setUp();

        $this->angler = Angler::factory()->create();
    }

    /** @test */
    public function it_cannot_create_a_duplicate_angler()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $anglerCopy = $this->angler->replicate();
        $anglerCopy->save();
    }
}
