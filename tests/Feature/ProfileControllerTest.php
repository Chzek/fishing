<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_profile_for_user_without_angler()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_shows_profile_for_user_with_angler_but_no_records()
    {
        $angler = Angler::factory()->create();
        $user = $angler->user;

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_shows_profile_with_telemetry_data_for_angler_with_records()
    {
        $angler = Angler::factory()->create();
        $user = $angler->user;
        \Fishinglog\Models\Record::factory()->create([
            'anglers_id' => $angler->id,
            'length' => 24.5,
            'weight' => 6.2,
            'released' => 1,
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertViewHas('totalInches', 24.5);
        $response->assertViewHas('totalFeet', 2.0);
        $response->assertViewHas('releaseRate', 100);
    }
}
