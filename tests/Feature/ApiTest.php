<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_fetch_records_api_index()
    {
        $record = Record::factory()->create();

        $response = $this->getJson('/api/v1/records');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'caught', 'length', 'weight', 'released']
            ]
        ]);
    }

    /** @test */
    public function it_can_fetch_lakes_api_index()
    {
        $lake = Lake::factory()->create();

        $response = $this->getJson('/api/v1/lakes');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'latitude', 'longitude']
            ]
        ]);
    }

    /** @test */
    public function it_can_fetch_anglers_api_index()
    {
        $angler = Angler::factory()->create();

        $response = $this->getJson('/api/v1/anglers');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'firstName', 'lastName', 'fullName']
            ]
        ]);
    }
}
