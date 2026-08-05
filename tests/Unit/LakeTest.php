<?php

namespace Tests\Unit;

use Fishinglog\Models\Lake;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LakeTest extends TestCase
{
    use RefreshDatabase;

    protected $lake;

    public function setUp(): void
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
