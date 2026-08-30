<?php

namespace Tests\Feature;

use Fishinglog\Livewire\Ui\LureSelector;
use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LureSelectorLivewireTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function lure_selector_mounts_with_empty_state()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lure1 = Lure::factory()->create([
            'brand' => 'Rapala',
            'name' => 'Shad Rap',
            'color' => 'Firetiger',
            'category' => 'Crankbait',
        ]);

        $lure2 = Lure::factory()->create([
            'brand' => 'Mepps',
            'name' => 'Aglia',
            'color' => 'Gold',
            'category' => 'Spinnerbait',
        ]);

        Livewire::test(LureSelector::class, [
            'name' => 'lures_id',
            'selectedId' => null,
            'placeholder' => 'Search lures...',
        ])
        ->assertStatus(200)
        ->assertSeeHtml('name="lures_id"')
        ->assertSeeHtml('value=""')
        ->assertSee('Rapala')
        ->assertSee('Shad Rap')
        ->assertSee('Mepps')
        ->assertSee('Aglia')
        ->assertSee('Crankbait')
        ->assertSee('Spinnerbait');
    }

    #[Test]
    public function lure_selector_mounts_with_preselected_lure()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lure = Lure::factory()->create([
            'brand' => 'Strike King',
            'name' => 'KVD Squarebill',
            'color' => 'Chartreuse Sexy Shad',
            'category' => 'Crankbait',
            'size' => '1.5',
            'weight' => '3/8 oz',
        ]);

        Livewire::test(LureSelector::class, [
            'name' => 'lures_id',
            'selectedId' => $lure->id,
        ])
        ->assertStatus(200)
        ->assertSeeHtml('value="' . $lure->id . '"')
        ->assertSee('Strike King')
        ->assertSee('KVD Squarebill')
        ->assertSee('Chartreuse Sexy Shad');
    }

    #[Test]
    public function lure_selector_filters_results_dynamically_by_search_query()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lure1 = Lure::factory()->create([
            'brand' => 'Acme',
            'name' => 'Kastmaster',
            'color' => 'Silver Chrome',
            'category' => 'Spoon',
        ]);

        $lure2 = Lure::factory()->create([
            'brand' => 'Berkley',
            'name' => 'PowerBait Minnow',
            'color' => 'Smelt',
            'category' => 'Soft Plastic',
        ]);

        Livewire::test(LureSelector::class)
            ->set('search', 'Kastmaster')
            ->assertSee('Acme')
            ->assertSee('Kastmaster')
            ->assertDontSee('PowerBait Minnow');
    }

    #[Test]
    public function lure_selector_filters_by_category()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lure1 = Lure::factory()->create([
            'brand' => 'Heddon',
            'name' => 'Zara Spook',
            'category' => 'Topwater',
        ]);

        $lure2 = Lure::factory()->create([
            'brand' => 'Yamamoto',
            'name' => 'Senko',
            'category' => 'Soft Plastic',
        ]);

        Livewire::test(LureSelector::class)
            ->call('setCategory', 'Topwater')
            ->assertSee('Zara Spook')
            ->assertDontSee('Senko');
    }

    #[Test]
    public function lure_selector_selects_lure_and_dispatches_event()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lure = Lure::factory()->create([
            'brand' => 'Rapala',
            'name' => 'Husky Jerk',
            'color' => 'Silver Blue',
            'category' => 'Crankbait',
        ]);

        Livewire::test(LureSelector::class)
            ->call('selectLure', $lure->id)
            ->assertDispatched('lure-selected', id: $lure->id)
            ->assertSet('selectedId', $lure->id)
            ->assertSeeHtml('value="' . $lure->id . '"')
            ->assertSee('Husky Jerk');
    }

    #[Test]
    public function lure_selector_clears_selection_and_dispatches_event()
    {
        $user = User::factory()->create();
        $this->be($user);

        $lure = Lure::factory()->create([
            'brand' => 'Rapala',
            'name' => 'Original Floating',
            'color' => 'Gold',
            'category' => 'Crankbait',
        ]);

        Livewire::test(LureSelector::class, [
            'selectedId' => $lure->id,
        ])
            ->assertSet('selectedId', $lure->id)
            ->call('clearSelection')
            ->assertDispatched('lure-cleared')
            ->assertSet('selectedId', null)
            ->assertSeeHtml('value=""');
    }

    #[Test]
    public function lure_selector_renders_in_record_create_and_edit_views()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id]);
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();
        $lure = Lure::factory()->create([
            'brand' => 'Mepps',
            'name' => 'Black Fury',
        ]);

        $record = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'lures_id' => $lure->id,
        ]);

        // Verify create form
        $createResponse = $this->actingAs($user)->get('/record/create');
        $createResponse->assertStatus(200);
        $createResponse->assertSeeLivewire(LureSelector::class);

        // Verify edit form
        $editResponse = $this->actingAs($user)->get("/record/{$record->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSeeLivewire(LureSelector::class);

        // Verify quick catch form
        $quickResponse = $this->actingAs($user)->get('/record/quick');
        $quickResponse->assertStatus(200);
        $quickResponse->assertSeeLivewire(LureSelector::class);
    }
}
