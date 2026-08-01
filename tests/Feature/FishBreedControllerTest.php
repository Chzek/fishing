<?php

namespace Tests\Feature;

use Fishinglog\Models\FishBreed;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FishBreedControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_update_fish_breed_without_image()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $breed = FishBreed::factory()->create([
            'name' => 'Old Name',
            'image' => 'old_image.jpg',
        ]);

        $response = $this->put('/fish/breed', [
            'id' => $breed->id,
            'name' => 'New Name',
            'fish_families_id' => $breed->fish_families_id,
        ]);

        $response->assertRedirect('/fish/' . $breed->id);

        $this->assertDatabaseHas('fish_breeds', [
            'id' => $breed->id,
            'name' => 'New Name',
            'image' => 'old_image.jpg',
        ]);
    }
}
