<?php

namespace Tests\Feature;

use Fishinglog\Models\Expedition;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function store_throws_model_not_found_if_user_has_no_angler()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/post', [
            'date' => '2023-01-01',
            'description' => 'Test post description',
            'expeditions_id' => 1,
        ]);

        $response->assertStatus(404);
    }
}
