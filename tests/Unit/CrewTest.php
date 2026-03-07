<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Fishinglog\Crew;

class CrewTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function crew_has_an_expedition_relationship_defined()
    {
        $crew = new Crew();
        
        // Crew belongs to an Expedition
        $this->assertEquals('Illuminate\Database\Eloquent\Relations\BelongsTo', get_class($crew->expedition()));
        $this->assertEquals('expeditions.id', $crew->expedition()->getQualifiedOwnerKeyName());
        $this->assertEquals('crews.expeditions_id', $crew->expedition()->getQualifiedForeignKeyName());
    }

    /** @test */
    public function crew_has_records_relationship_defined()
    {
        $crew = new Crew();
        
        // Crew has many Records based on anglers_id
        $this->assertEquals('Illuminate\Database\Eloquent\Relations\HasMany', get_class($crew->records()));
        $this->assertEquals('records.anglers_id', $crew->records()->getQualifiedForeignKeyName());
        $this->assertEquals('crews.anglers_id', $crew->records()->getQualifiedParentKeyName());
    }
}
