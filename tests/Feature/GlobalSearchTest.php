<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unauthenticated_user_cannot_access_search()
    {
        $response = $this->get('/search');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_search_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/search');

        $response->assertStatus(200);
        $response->assertSee('Quick Command Actions');
    }

    public function test_search_matches_static_command_action_aliases()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/search?q=New+Angler');
        $response->assertStatus(200);
        $response->assertSee('Create New Angler Profile');

        $responseQuick = $this->actingAs($user)->get('/search?q=Quick+Catch');
        $responseQuick->assertStatus(200);
        $responseQuick->assertSee('Launch Quick Catch Mode');
    }

    public function test_search_queries_angler_models()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create([
            'firstName' => 'Geren',
            'lastName' => 'Mroczek',
        ]);

        $response = $this->actingAs($user)->get('/search?q=Geren');
        $response->assertStatus(200);
        $response->assertSee($angler->fullName);
    }
}
