<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnglerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $angler;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->angler = Angler::factory()->create();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_create_an_angler()
    {
        $this->be($this->user);

        $this->post('/angler', $this->angler->toArray());
        $this->assertDatabaseHas('anglers', $this->angler->toArray());
    }

    #[Test]
    public function it_can_view_all_anglers()
    {
        $this->be($this->user);

        $response = $this->get('/angler');
        $response->assertSee($this->angler->lastName);
    }

    #[Test]
    public function it_can_view_an_angler()
    {
        $this->be($this->user);

        $response = $this->get('/angler/' . $this->angler->id);
        $response->assertSee($this->angler->lastName);
    }

    #[Test]
    public function it_can_update_an_angler_without_avatar()
    {
        $this->be($this->user);

        $response = $this->put('/angler', [
            'id' => $this->angler->id,
            'firstName' => 'Updated',
            'middleName' => $this->angler->middleName,
            'lastName' => $this->angler->lastName,
            'user_id' => $this->angler->user_id,
            'birthdate' => $this->angler->birthdate,
        ]);

        $response->assertRedirect('/angler/'.$this->angler->id);

        $this->assertDatabaseHas('anglers', [
            'id' => $this->angler->id,
            'firstName' => 'Updated',
        ]);
    }

    #[Test]
    public function it_can_view_angler_profile()
    {
        $this->be($this->user);

        $response = $this->get('/angler/' . $this->angler->id . '/profile');
        $response->assertStatus(200);
        $response->assertSeeText($this->angler->firstName);
        $response->assertSeeText($this->angler->lastName);
        $response->assertSeeText('Angler Catches Logbook');
    }

    #[Test]
    public function it_can_create_an_angler_without_middle_name()
    {
        $this->be($this->user);

        $data = [
            'firstName' => 'Sammy',
            'middleName' => null,
            'lastName' => 'Smith',
        ];

        $response = $this->post('/angler', $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('anglers', [
            'firstName' => 'Sammy',
            'middleName' => null,
            'lastName' => 'Smith',
        ]);
        $angler = Angler::where('firstName', 'Sammy')->first();
        $this->assertEquals('Sammy Smith', $angler->fullName);
    }

    public function test_full_name_renders_middle_initial()
    {
        $angler = Angler::factory()->create([
            'firstName' => 'Geren',
            'middleName' => 'Perry',
            'lastName' => 'Mroczek',
        ]);

        $this->assertEquals('Geren P. Mroczek', $angler->fullName);
        $this->assertEquals('Mroczek, Geren P.', $angler->formalName);
    }
}

