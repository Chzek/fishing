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

    /**
     * A basic unit test create angler.
     *
     * @return void
     */
    public function testCreateAngler()
    {
        $angler = new Angler;
        $angler->firstName = 'Geren';
        $angler->middleName = 'Pierce';
        $angler->lastName = 'Mroczek';
        $angler->birthdate = '1982-03-05';

        $angler->save();

        $this->assertDatabaseHas('anglers', [ 'firstName' => 'Geren' ]);
    }

    /**
     * A basic unit test duplicate angler.
     */
    public function testDuplicateAngler()
    {
        $angler = new Angler;
        $angler->firstName = 'Geren';
        $angler->middleName = 'Pierce';
        $angler->lastName = 'Mroczek';

        $angler->save();

        $anglers = Angler::where('firstName', 'Geren')->get();

        $this->assertSame($anglers->count(), 1);

        $angler = new Angler;
        $angler->firstName = 'Geren';
        $angler->middleName = 'Pierce';
        $angler->lastName = 'Mroczek';

        $this->expectException(\Illuminate\Database\QueryException::class);
        $angler->save(); 
    }

    
}
