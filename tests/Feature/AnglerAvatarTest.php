<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnglerAvatarTest extends TestCase
{
    use DatabaseTransactions;

    protected $angler;

    public function setUp(): void
    {
        parent::setUp();
        $this->angler = Angler::factory()->create();
    }

    #[Test]
    public function it_can_update_avatar_for_logged_in_angler()
    {
        Storage::fake('public');

        $user = $this->angler->user;

        $response = $this->actingAs($user)
            ->post('/angler/avatar', [
                'avatar' => UploadedFile::fake()->image('photo.jpg', 200, 200),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->angler->refresh();

        $this->assertNotNull($this->angler->avatar);
        $this->assertStringStartsWith('avatar_', $this->angler->avatar);
        $this->assertStringEndsWith('.jpg', $this->angler->avatar);

        Storage::assertExists('avatars/' . $this->angler->avatar);
    }

    #[Test]
    public function it_rejects_non_image_files()
    {
        $user = $this->angler->user;

        $response = $this->actingAs($user)
            ->post('/angler/avatar', [
                'avatar' => UploadedFile::fake()->create('document.pdf', 100),
            ]);

        $response->assertSessionHasErrors('avatar');
    }

    #[Test]
    public function it_requires_an_avatar_file()
    {
        $user = $this->angler->user;

        $response = $this->actingAs($user)
            ->post('/angler/avatar', []);

        $response->assertSessionHasErrors('avatar');
    }

    #[Test]
    public function unauthenticated_users_cannot_upload_avatar()
    {
        $response = $this->post('/angler/avatar', [
            'avatar' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $response->assertRedirect('/login');
    }
}
