<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

use Fishinglog\Angler;
use Fishinglog\User;

class AnglerAvatarTest extends TestCase
{
    use DatabaseMigrations;

    protected $angler;

    public function setUp(): void
    {
        parent::setUp();
        $this->angler = Angler::factory()->create();
    }

    /** @test */
    public function it_can_update_avatar_for_logged_in_angler()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        // The factory creates a User linked to the Angler
        $user = $this->angler->user;

        $response = $this->actingAs($user)
            ->post('/angler/avatar', [
                'avatar' => UploadedFile::fake()->image('photo.jpg', 200, 200),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Reload the angler from the database
        $this->angler->refresh();

        // Assert the avatar field was updated
        $this->assertNotNull($this->angler->avatar);
        $this->assertStringStartsWith('avatar_', $this->angler->avatar);
        $this->assertStringEndsWith('.jpg', $this->angler->avatar);

        // Assert the file was stored
        Storage::assertExists('avatars/' . $this->angler->avatar);
    }

    /** @test */
    public function it_rejects_non_image_files()
    {
        $user = $this->angler->user;

        $response = $this->actingAs($user)
            ->post('/angler/avatar', [
                'avatar' => UploadedFile::fake()->create('document.pdf', 100),
            ]);

        $response->assertSessionHasErrors('avatar');
    }

    /** @test */
    public function it_requires_an_avatar_file()
    {
        $user = $this->angler->user;

        $response = $this->actingAs($user)
            ->post('/angler/avatar', []);

        $response->assertSessionHasErrors('avatar');
    }

    /** @test */
    public function unauthenticated_users_cannot_upload_avatar()
    {
        $response = $this->post('/angler/avatar', [
            'avatar' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $response->assertRedirect('/login');
    }
}
