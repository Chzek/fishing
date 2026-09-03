<?php

namespace Tests\Feature;

use Fishinglog\Livewire\Tacklebox\LureCatalog;
use Fishinglog\Models\Lure;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LureCatalogLivewireTest extends TestCase
{
    use RefreshDatabase;

    public function test_lure_catalog_mounts_with_default_state(): void
    {
        $user = User::factory()->create();

        Lure::create([
            'name' => 'Shad Rap 05',
            'brand' => 'Rapala',
            'category' => 'Crankbait',
            'color' => 'Perch',
            'depth_range' => '6-8 ft',
        ]);

        Livewire::actingAs($user)
            ->test(LureCatalog::class)
            ->assertStatus(200)
            ->assertSee('Digital Tackle Box')
            ->assertSee('Shad Rap 05')
            ->assertSee('Rapala')
            ->assertSee('Perch')
            ->assertSee('Crankbait Tray');
    }

    public function test_lure_catalog_filters_by_category_tab(): void
    {
        $user = User::factory()->create();

        $crank = Lure::create([
            'name' => 'Shad Rap',
            'brand' => 'Rapala',
            'category' => 'Crankbait',
            'color' => 'Firetiger',
        ]);

        $softPlastic = Lure::create([
            'name' => 'Senko 5"',
            'brand' => 'Yamamoto',
            'category' => 'Soft Plastic',
            'color' => 'Green Pumpkin',
        ]);

        Livewire::actingAs($user)
            ->test(LureCatalog::class)
            ->set('selectedCategory', 'Crankbait')
            ->assertSee('Shad Rap')
            ->assertDontSee('Senko 5"')
            ->set('selectedCategory', 'Soft Plastic')
            ->assertSee('Senko 5"')
            ->assertDontSee('Shad Rap');
    }

    public function test_lure_catalog_filters_by_search_query(): void
    {
        $user = User::factory()->create();

        Lure::create([
            'name' => 'Vision 110',
            'brand' => 'Megabass',
            'category' => 'Crankbait',
            'color' => 'Elegy Bone',
        ]);

        Lure::create([
            'name' => 'Fat Swing Impact',
            'brand' => 'Keitech',
            'category' => 'Swimbait',
            'color' => 'Sight Flash',
        ]);

        Livewire::actingAs($user)
            ->test(LureCatalog::class)
            ->set('search', 'Megabass')
            ->assertSee('Vision 110')
            ->assertDontSee('Fat Swing Impact')
            ->set('search', 'Keitech')
            ->assertSee('Fat Swing Impact')
            ->assertDontSee('Vision 110')
            ->set('search', 'Sight Flash')
            ->assertSee('Fat Swing Impact')
            ->assertDontSee('Vision 110');
    }

    public function test_lure_catalog_filters_by_depth_tier(): void
    {
        $user = User::factory()->create();

        Lure::create([
            'name' => 'Whopper Plopper 110',
            'brand' => 'River2Sea',
            'category' => 'Topwater',
            'color' => 'Loon',
            'depth_range' => 'Topwater',
        ]);

        Lure::create([
            'name' => 'DT-16',
            'brand' => 'Rapala',
            'category' => 'Crankbait',
            'color' => 'Ike\'s Smash',
            'depth_range' => '16 ft',
        ]);

        Livewire::actingAs($user)
            ->test(LureCatalog::class)
            ->set('selectedDepth', 'surface_0')
            ->assertSee('Whopper Plopper 110')
            ->assertDontSee('DT-16')
            ->set('selectedDepth', 'deep_10_20')
            ->assertSee('DT-16')
            ->assertDontSee('Whopper Plopper 110');
    }

    public function test_lure_catalog_toggles_category_trays_and_all(): void
    {
        $user = User::factory()->create();

        Lure::create([
            'name' => 'Shad Rap',
            'brand' => 'Rapala',
            'category' => 'Crankbait',
            'color' => 'Perch',
        ]);

        Livewire::actingAs($user)
            ->test(LureCatalog::class)
            ->assertSet('allExpanded', true)
            ->call('toggleCategory', 'Crankbait')
            ->assertSet('openCategories.Crankbait', false)
            ->call('toggleAllTrays')
            ->assertSet('allExpanded', false);
    }

    public function test_lure_catalog_quick_adds_colorway_variants(): void
    {
        $user = User::factory()->create();

        Lure::create([
            'name' => 'Shad Rap 05',
            'brand' => 'Rapala',
            'category' => 'Crankbait',
            'color' => 'Perch',
            'depth_range' => '6-8 ft',
        ]);

        Livewire::actingAs($user)
            ->test(LureCatalog::class)
            ->call('openAddVariantModal', 'Rapala', 'Shad Rap 05', 'Crankbait', '6-8 ft', '3/16 oz')
            ->assertSet('showAddVariantModal', true)
            ->set('newVariantColors', 'Firetiger, Blue Chrome')
            ->call('saveVariant')
            ->assertSet('showAddVariantModal', false)
            ->assertSee('Added 2 new colorway variant(s)')
            ->assertSee('Firetiger')
            ->assertSee('Blue Chrome');

        $this->assertDatabaseHas('lures', [
            'name' => 'Shad Rap 05',
            'brand' => 'Rapala',
            'color' => 'Firetiger',
        ]);

        $this->assertDatabaseHas('lures', [
            'name' => 'Shad Rap 05',
            'brand' => 'Rapala',
            'color' => 'Blue Chrome',
        ]);
    }

    public function test_lure_catalog_dispatches_quick_catch_modal_event(): void
    {
        $user = User::factory()->create();

        $lure = Lure::create([
            'name' => 'Vision 110',
            'brand' => 'Megabass',
            'category' => 'Crankbait',
            'color' => 'Elegy Bone',
        ]);

        Livewire::actingAs($user)
            ->test(LureCatalog::class)
            ->call('logCatchWithLure', (string)$lure->id)
            ->assertDispatched('open-quick-catch', lure_id: (string)$lure->id);
    }

    public function test_lure_catalog_deletes_variant(): void
    {
        $user = User::factory()->create();

        $lure = Lure::create([
            'name' => 'Super Spook',
            'brand' => 'Heddon',
            'category' => 'Topwater',
            'color' => 'Bone',
        ]);

        Livewire::actingAs($user)
            ->test(LureCatalog::class)
            ->call('deleteVariant', (string)$lure->id)
            ->assertSee('Deleted variant');

        $this->assertSoftDeleted('lures', [
            'id' => $lure->id,
        ]);
    }
}
