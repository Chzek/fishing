<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Fishinglog\User;
use Fishinglog\FishBreed;

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
            'image' => 'old_image.jpg'
        ]);

        $response = $this->put('/fish/breed', [
            'id' => $breed->id,
            'name' => 'New Name',
            'fish_families_id' => $breed->fish_families_id,
            // no image provided
        ]);

        $response->assertRedirect('/fish/' . $breed->id);
        
        $this->assertDatabaseHas('fish_breeds', [
            'id' => $breed->id,
            'name' => 'New Name',
            'image' => 'old_image.jpg' // Should remain unchanged
        ]);
    }
}
