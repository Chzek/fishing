<?php

namespace Tests\Unit;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Record;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RecordTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_has_an_expedition_method()
    {
        $record = new Record();
        $this->assertTrue(method_exists($record, 'expedition'));
    }
}
