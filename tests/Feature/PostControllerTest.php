<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Fishinglog\User;
use Fishinglog\Expedition;

class PostControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function store_throws_model_not_found_if_user_has_no_angler()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Required explicitly to catch ModelNotFoundException in feature tests sometimes,
        // or we can assert the response status is 404 (which Laravel converts it to).
        $response = $this->post('/post', [
            'date' => '2023-01-01',
            'description' => 'Test post description',
            'expeditions_id' => 1
        ]);

        $response->assertStatus(404);
    }
}
