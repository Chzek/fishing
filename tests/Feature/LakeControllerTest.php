<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Lake;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LakeControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $lake;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->lake = Lake::factory()->create();
    }

    #[Test]
    public function unauthenticated_user_cannot_access_lakes()
    {
        $response = $this->get('/lake');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_user_can_view_lakes_index()
    {
        $this->actingAs($this->user);

        $response = $this->get('/lake');
        $response->assertStatus(200);
        $response->assertSee($this->lake->name);
    }

    #[Test]
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

    #[Test]
    public function authenticated_user_can_view_a_lake()
    {
        $this->actingAs($this->user);

        $response = $this->get('/lake/' . $this->lake->id);
        $response->assertStatus(200);
        $response->assertSee($this->lake->name);
        $response->assertSee('Location, Bathymetry & Topo Map');
        $response->assertSee('Bathymetry & Contours', false);
    }

    #[Test]
    public function authenticated_user_can_view_lake_with_top_catches_strip()
    {
        $this->actingAs($this->user);

        $species = \Fishinglog\Models\FishBreed::factory()->create(['name' => 'Walleye']);
        $angler = \Fishinglog\Models\Angler::factory()->create(['firstName' => 'Samantha', 'middleName' => '', 'lastName' => 'Reed']);

        \Fishinglog\Models\Record::factory()->create([
            'lakes_id' => $this->lake->id,
            'fish_breeds_id' => $species->id,
            'anglers_id' => $angler->id,
            'length' => 29.5,
            'weight' => 9.25,
            'caught' => '2026-07-20',
        ]);

        $response = $this->get('/lake/' . $this->lake->id);
        $response->assertStatus(200);
        $response->assertSee('Top 5 Lake Catches');
        $response->assertSee('29.5"', false);
        $response->assertSee('9.25 lbs');
        $response->assertSee('105.4 pts');
        $response->assertSee('Samantha Reed');
        $response->assertSee('Walleye');
        $response->assertSee('Jul 2026');
    }

    #[Test]
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

    #[Test]
    public function authenticated_user_can_view_create_lake_form()
    {
        $this->actingAs($this->user);

        $response = $this->get('/lake/create');
        $response->assertStatus(200);
        $response->assertSee('Log New Lake / Waterbody');
        $response->assertSee('Use Current GPS Location');
        $response->assertSee('lake-picker-map');
    }

    #[Test]
    public function authenticated_user_can_view_edit_lake_form()
    {
        $this->actingAs($this->user);

        $response = $this->get('/lake/' . $this->lake->id . '/edit');
        $response->assertStatus(200);
        $response->assertSee('Edit Lake: ' . $this->lake->name);
        $response->assertSee('Use Current GPS Location');
        $response->assertSee('lake-picker-map');
    }
}
