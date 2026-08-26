<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RecordGeoLocationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_store_record_with_latitude_and_longitude()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create();
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();

        $response = $this->actingAs($user)->post('/record', [
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 22.5,
            'weight' => 5.2,
            'latitude' => 48.01234567,
            'longitude' => -84.82194567,
            'released' => 1,
            'caught' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(302);

        $record = Record::latest()->first();
        $this->assertEquals(22.5, $record->length);
        $this->assertEquals(48.01234567, (float) $record->latitude);
        $this->assertEquals(-84.82194567, (float) $record->longitude);
    }

    public function test_can_access_offline_review_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/record/offline-review');

        $response->assertStatus(200);
        $response->assertSeeText('Offline Field Sync');
        $response->assertSeeText('Catch Inspection');
    }
}
