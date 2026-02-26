<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Fishinglog\Record;

class RecordTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_does_not_have_an_expedition_method()
    {
        $record = new Record();
        $this->assertFalse(method_exists($record, 'expedition'));
    }
}
