<?php

namespace Tests\Unit;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Angler;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AnglerTest extends TestCase
{
    use DatabaseTransactions;

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

    #[Test]
    public function it_correctly_formats_full_name_and_formal_name()
    {
        $anglerWithMiddle = Angler::create([
            'firstName' => 'John',
            'middleName' => 'David',
            'lastName' => 'Doe',
        ]);

        $this->assertEquals('John D. Doe', $anglerWithMiddle->full_name);
        $this->assertEquals('John D. Doe', $anglerWithMiddle->fullName);
        $this->assertEquals('Doe, John D.', $anglerWithMiddle->formal_name);
        $this->assertEquals('Doe, John D.', $anglerWithMiddle->formalName);

        $anglerWithoutMiddle = Angler::create([
            'firstName' => 'Jane',
            'middleName' => '',
            'lastName' => 'Smith',
        ]);

        $this->assertEquals('Jane Smith', $anglerWithoutMiddle->full_name);
        $this->assertEquals('Smith, Jane', $anglerWithoutMiddle->formal_name);
    }

    #[Test]
    public function it_provides_defensive_casing_accessors_for_properties()
    {
        $angler = Angler::create([
            'firstName' => 'Robert',
            'middleName' => 'Paul',
            'lastName' => 'Johnson',
        ]);

        $this->assertEquals('Robert', $angler->firstName);
        $this->assertEquals('Robert', $angler->firstname);
        $this->assertEquals('Johnson', $angler->lastName);
        $this->assertEquals('Johnson', $angler->lastname);
        $this->assertEquals('Paul', $angler->middleName);
        $this->assertEquals('Paul', $angler->middlename);
        $this->assertEquals('Robert P. Johnson', $angler->name);
        $this->assertEquals('Robert P. Johnson', $angler->full_name);
    }

}
