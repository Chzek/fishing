<?php

namespace Tests\Unit;

use Fishinglog\Models\Angler;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AnglerTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_cannot_create_a_duplicate_angler()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $angler = Angler::factory()->create();
        $anglerCopy = $angler->replicate();
        $anglerCopy->save();
    }

    #[Test]
    public function it_correctly_formats_full_name_and_formal_name()
    {
        $anglerWithMiddle = Angler::create([
            'firstName' => 'John',
            'middleName' => 'David',
            'lastName' => 'Doe',
        ]);

        $this->assertSame('John D. Doe', $anglerWithMiddle->full_name);
        $this->assertSame('John D. Doe', $anglerWithMiddle->fullName);
        $this->assertSame('Doe, John D.', $anglerWithMiddle->formal_name);
        $this->assertSame('Doe, John D.', $anglerWithMiddle->formalName);

        $anglerWithoutMiddle = Angler::create([
            'firstName' => 'Jane',
            'middleName' => '',
            'lastName' => 'Smith',
        ]);

        $this->assertSame('Jane Smith', $anglerWithoutMiddle->full_name);
        $this->assertSame('Smith, Jane', $anglerWithoutMiddle->formal_name);
    }

    #[Test]
    public function it_provides_defensive_casing_accessors_for_properties()
    {
        $angler = Angler::create([
            'firstName' => 'Robert',
            'middleName' => 'Paul',
            'lastName' => 'Johnson',
        ]);

        $this->assertSame('Robert', $angler->firstName);
        $this->assertSame('Robert', $angler->firstname);
        $this->assertSame('Johnson', $angler->lastName);
        $this->assertSame('Johnson', $angler->lastname);
        $this->assertSame('Paul', $angler->middleName);
        $this->assertSame('Paul', $angler->middlename);
        $this->assertSame('Robert P. Johnson', $angler->name);
        $this->assertSame('Robert P. Johnson', $angler->full_name);
    }
}
