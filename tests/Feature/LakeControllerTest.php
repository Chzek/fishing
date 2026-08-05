<?php

namespace Tests\Feature;

use Fishinglog\Models\Lake;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LakeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $lake;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->lake = Lake::factory()->create();
    }

    /** @test */
    public function unauthenticated_user_cannot_access_lakes()
    {
        $response = $this->get('/lake');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_view_lakes_index()
    {
        $this->actingAs($this->user);

        $response = $this->get('/lake');
        $response->assertStatus(200);
        $response->assertSee($this->lake->name);
    }

    /** @test */
    public function authenticated_user_can_create_lake()
    {
        $this->actingAs($this->user);

        $response = $this->post('/lake', [
            'name' => 'Crystal Lake',
            'latitude' => 45.123456,
            'longitude' => -93.123456,
        ]);

        $response->assertRedirect('/lake');
        $this->assertDatabaseHas('lakes', [
            'name' => 'Crystal Lake',
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_a_lake()
    {
        $this->actingAs($this->user);

        $response = $this->get('/lake/' . $this->lake->id);
        $response->assertStatus(200);
        $response->assertSee($this->lake->name);
    }

    /** @test */
    public function authenticated_user_can_update_a_lake()
    {
        $this->actingAs($this->user);

        $response = $this->put('/lake', [
            'id' => $this->lake->id,
            'name' => 'Updated Lake Name',
            'latitude' => 46.000000,
            'longitude' => -94.000000,
        ]);

        $response->assertRedirect('/lake/' . $this->lake->id);
        $this->assertDatabaseHas('lakes', [
            'id' => $this->lake->id,
            'name' => 'Updated Lake Name',
        ]);
    }
}
