<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Lure;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LureControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $lure;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->lure = Lure::factory()->create();
    }

    #[Test]
    public function unauthenticated_user_cannot_access_lures()
    {
        $response = $this->get('/lure');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function authenticated_user_can_view_lures_index()
    {
        $this->actingAs($this->user);

        $response = $this->get('/lure');
        $response->assertStatus(200);
        $response->assertSee($this->lure->name);
    }

    #[Test]
    public function authenticated_user_can_create_lure()
    {
        $this->actingAs($this->user);

        $response = $this->post('/lure', [
            'name' => 'Spinnerbait',
            'color' => 'Chartreuse',
            'size' => '0.5 oz',
        ]);

        $response->assertRedirect('/lure');
        $this->assertDatabaseHas('lures', [
            'name' => 'Spinnerbait',
            'color' => 'Chartreuse',
        ]);
    }

    #[Test]
    public function authenticated_user_can_view_a_lure()
    {
        $this->actingAs($this->user);

        $response = $this->get('/lure/' . $this->lure->id);
        $response->assertStatus(200);
        $response->assertSee($this->lure->name);
    }

    #[Test]
    public function authenticated_user_can_update_a_lure()
    {
        $this->actingAs($this->user);

        $response = $this->put('/lure', [
            'id' => $this->lure->id,
            'name' => 'Updated Lure Name',
            'color' => 'Black/Blue',
            'size' => '0.75 oz',
        ]);

        $response->assertRedirect('/lure/' . $this->lure->id);
        $this->assertDatabaseHas('lures', [
            'id' => $this->lure->id,
            'name' => 'Updated Lure Name',
        ]);
    }
}
