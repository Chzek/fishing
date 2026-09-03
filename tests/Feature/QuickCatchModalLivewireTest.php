<?php

namespace Tests\Feature;

use Fishinglog\Livewire\Modals\QuickCatchModal;
use Fishinglog\Models\Angler;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Lure;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuickCatchModalLivewireTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function quick_catch_modal_mounts_with_default_state()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(QuickCatchModal::class)
            ->assertSet('isOpen', false)
            ->assertSet('anglers_id', (string) $angler->id)
            ->assertSet('caught', date('Y-m-d'))
            ->assertSet('released', true);
    }

    #[Test]
    public function quick_catch_modal_opens_and_prefills_contextual_parameters()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id]);
        $lake = Lake::factory()->create(['latitude' => 45.123456, 'longitude' => -78.654321]);
        $breed = FishBreed::factory()->create();
        $expedition = Expedition::create([
            'description' => 'Annual Summer Trip',
            'start' => date('Y-m-d'),
            'finish' => date('Y-m-d'),
        ]);
        $lure = Lure::factory()->create();

        Livewire::actingAs($user)
            ->test(QuickCatchModal::class)
            ->dispatch('open-quick-catch', [
                'lake_id' => $lake->id,
                'species_id' => $breed->id,
                'expedition_id' => $expedition->id,
                'lure_id' => $lure->id,
                'latitude' => 45.123456,
                'longitude' => -78.654321,
            ])
            ->assertSet('isOpen', true)
            ->assertSet('lakes_id', (string) $lake->id)
            ->assertSet('fish_breeds_id', (string) $breed->id)
            ->assertSet('expeditions_id', (string) $expedition->id)
            ->assertSet('lures_id', (string) $lure->id)
            ->assertSet('latitude', 45.123456)
            ->assertSet('longitude', -78.654321);
    }

    #[Test]
    public function quick_catch_modal_validates_required_fields()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(QuickCatchModal::class)
            ->set('isOpen', true)
            ->set('anglers_id', '')
            ->set('lakes_id', '')
            ->set('fish_breeds_id', '')
            ->set('length', null)
            ->call('save')
            ->assertHasErrors(['anglers_id', 'lakes_id', 'fish_breeds_id', 'length']);
    }

    #[Test]
    public function quick_catch_modal_saves_catch_and_dispatches_events()
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id]);
        $lake = Lake::factory()->create();
        $breed = FishBreed::factory()->create();
        $lure = Lure::factory()->create();

        Livewire::actingAs($user)
            ->test(QuickCatchModal::class)
            ->set('anglers_id', (string) $angler->id)
            ->set('lakes_id', (string) $lake->id)
            ->set('fish_breeds_id', (string) $breed->id)
            ->set('lures_id', (string) $lure->id)
            ->set('length', 21.5)
            ->set('weight', 5.3)
            ->set('caught', date('Y-m-d'))
            ->set('released', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('statusType', 'success')
            ->assertDispatched('catch-saved')
            ->assertDispatched('refresh-records')
            ->assertDispatched('refresh-map');

        $this->assertDatabaseHas('records', [
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'lures_id' => $lure->id,
            'length' => 21.5,
            'weight' => 5.3,
            'released' => 1,
        ]);
    }

    #[Test]
    public function quick_catch_modal_closes_and_clears_messages()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(QuickCatchModal::class)
            ->set('isOpen', true)
            ->set('statusMessage', 'Old message')
            ->dispatch('close-quick-catch')
            ->assertSet('isOpen', false)
            ->assertSet('statusMessage', null);
    }
}
