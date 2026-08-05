<?php

namespace Tests\Unit;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Angler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnglerTest extends TestCase
{
    use RefreshDatabase;

    protected $angler;

    public function setUp(): void
    {
        parent::setUp();

        $this->angler = Angler::factory()->create();
    }

    #[Test]
    public function it_cannot_create_a_duplicate_angler()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $anglerCopy = $this->angler->replicate();
        $anglerCopy->save();
    }
}
