<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_profile_for_user_without_angler()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_shows_profile_for_user_with_angler_but_no_records()
    {
        $angler = Angler::factory()->create();
        $user = $angler->user;

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }
}
