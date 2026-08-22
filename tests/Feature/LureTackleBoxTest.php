<?php

namespace Tests\Feature;

use Database\Seeders\LureSeeder;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LureTackleBoxTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_view_tackle_box_index()
    {
        $user = User::factory()->create();

        Lure::create([
            'brand' => 'Rapala',
            'name' => 'Shad Rap',
            'category' => 'Crankbait',
            'color' => 'Firetiger',
            'size' => '5/16 oz.',
        ]);

        $response = $this->actingAs($user)->get('/lure');

        $response->assertStatus(200);
        $response->assertSee('Digital Tackle Box');
        $response->assertSee('Shad Rap');
        $response->assertSee('Firetiger');
        $response->assertSee('Crankbait');
    }

    #[Test]
    public function lure_seeder_imports_master_catalog_without_duplicates()
    {
        $this->seed(LureSeeder::class);

        $initialCount = Lure::count();
        $this->assertGreaterThan(10, $initialCount);

        // Re-run seeder to verify no duplicates
        $this->seed(LureSeeder::class);
        $this->assertEquals($initialCount, Lure::count());

        $this->assertDatabaseHas('lures', [
            'name' => 'Shad Rap',
            'color' => 'Firetiger',
            'category' => 'Crankbait',
        ]);
    }

    #[Test]
    public function user_can_create_lure_with_tackle_taxonomy()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/lure', [
            'brand' => 'Keitech',
            'name' => 'Fat Swing Impact',
            'category' => 'Swimbait',
            'color' => 'Electric Shad',
            'size' => '3.8"',
            'weight' => '1/4 oz.',
            'depth_range' => 'All Depths',
        ]);

        $response->assertRedirect('/lure');
        $this->assertDatabaseHas('lures', [
            'brand' => 'Keitech',
            'name' => 'Fat Swing Impact',
            'category' => 'Swimbait',
            'color' => 'Electric Shad',
        ]);
    }

    #[Test]
    public function user_can_batch_create_multi_color_lure_variants()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/lure/batch', [
            'brand' => 'Gary Yamamoto',
            'name' => '5" Senko',
            'category' => 'Soft Plastic',
            'colors_input' => 'Green Pumpkin, Watermelon Red, Black Blue Flake',
            'size' => '5"',
            'depth_range' => 'Bottom',
        ]);

        $response->assertRedirect('/lure');

        $this->assertDatabaseHas('lures', [
            'name' => '5" Senko',
            'color' => 'Green Pumpkin',
            'category' => 'Soft Plastic',
        ]);
        $this->assertDatabaseHas('lures', [
            'name' => '5" Senko',
            'color' => 'Watermelon Red',
        ]);
        $this->assertDatabaseHas('lures', [
            'name' => '5" Senko',
            'color' => 'Black Blue Flake',
        ]);
    }

    #[Test]
    public function user_can_filter_tackle_box_by_category()
    {
        $user = User::factory()->create();

        Lure::create(['name' => 'Shad Rap', 'category' => 'Crankbait', 'color' => 'Firetiger', 'size' => '2"']);
        Lure::create(['name' => 'Senko', 'category' => 'Soft Plastic', 'color' => 'Green Pumpkin', 'size' => '5"']);

        $response = $this->actingAs($user)->get('/lure?category=Crankbait');

        $response->assertStatus(200);
        $response->assertSee('Shad Rap');
        $response->assertDontSee('Senko');
    }

    #[Test]
    public function lure_show_page_displays_tackle_telemetry_and_catches()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create();
        $breed = FishBreed::factory()->create(['name' => 'Walleye']);
        $lake = Lake::factory()->create(['name' => 'Oneida Lake']);

        $lure = Lure::create([
            'brand' => 'Rapala',
            'name' => 'Shad Rap',
            'category' => 'Crankbait',
            'color' => 'Firetiger',
            'size' => '2"',
        ]);


        Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'lures_id' => $lure->id,
            'length' => 24.5,
            'caught' => now(),
        ]);

        $response = $this->actingAs($user)->get('/lure/' . $lure->id);

        $response->assertStatus(200);
        $response->assertSee('Shad Rap');
        $response->assertSee('Firetiger');
        $response->assertSee('Walleye');
        $response->assertSee('Oneida Lake');
    }
}
