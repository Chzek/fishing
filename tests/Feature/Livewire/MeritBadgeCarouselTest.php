<?php

namespace Tests\Feature\Livewire;

use Database\Seeders\BadgeCatalogSeeder;
use Fishinglog\Livewire\Components\MeritBadgeCarousel;
use Fishinglog\Models\Angler;
use Fishinglog\Models\AnglerBadge;
use Fishinglog\Models\Badge;
use Fishinglog\Models\Expedition;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Photo;
use Fishinglog\Models\Record;
use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MeritBadgeCarouselTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BadgeCatalogSeeder::class);
    }

    #[Test]
    public function test_merit_badge_carousel_mounts_in_empty_state_without_errors(): void
    {
        Livewire::test(MeritBadgeCarousel::class)
            ->assertStatus(200)
            ->assertSee('Merit Badge Carousel')
            ->assertSee('No merit badges earned yet');
    }

    #[Test]
    public function test_merit_badge_carousel_renders_passed_badges_collection(): void
    {
        $badge = Badge::first();
        $this->assertNotNull($badge);

        Livewire::test(MeritBadgeCarousel::class, [
            'badges' => collect([$badge]),
            'title' => 'Test Merit Badges',
        ])
            ->assertStatus(200)
            ->assertSee('Test Merit Badges')
            ->assertSee($badge->name)
            ->assertSee('+' . $badge->points . ' pts')
            ->assertSee('1 Earned');
    }

    #[Test]
    public function test_merit_badge_carousel_binds_to_angler_and_computes_points(): void
    {
        $angler = Angler::factory()->create();
        $badges = Badge::take(2)->get();

        foreach ($badges as $badge) {
            AnglerBadge::create([
                'anglers_id' => $angler->id,
                'badge_id' => $badge->id,
                'points' => $badge->points,
                'awarded_at' => now(),
                'trigger_summary' => 'Scouting catch milestone',
            ]);
        }

        $totalExpectedPoints = $badges->sum('points');

        Livewire::test(MeritBadgeCarousel::class, [
            'angler' => $angler,
            'title' => 'Angler Accolades',
        ])
            ->assertStatus(200)
            ->assertSee('Angler Accolades')
            ->assertSee((string) $totalExpectedPoints)
            ->assertSee($badges[0]->name)
            ->assertSee($badges[1]->name)
            ->assertSee('Scouting catch milestone');
    }

    #[Test]
    public function test_merit_badge_carousel_binds_to_expedition(): void
    {
        $angler = Angler::factory()->create();
        $expedition = Expedition::create([
            'description' => 'Great Bear Lake Expedition',
            'start' => '2026-07-01',
            'finish' => '2026-07-07',
        ]);

        $badge = Badge::first();

        AnglerBadge::create([
            'anglers_id' => $angler->id,
            'badge_id' => $badge->id,
            'expedition_id' => $expedition->id,
            'points' => $badge->points,
            'awarded_at' => now(),
            'trigger_summary' => 'Expedition lunker landed',
        ]);

        Livewire::test(MeritBadgeCarousel::class, [
            'expedition' => $expedition,
            'title' => 'Expedition Merit Badges',
        ])
            ->assertStatus(200)
            ->assertSee('Expedition Merit Badges')
            ->assertSee($badge->name)
            ->assertSee('Expedition lunker landed');
    }

    #[Test]
    public function test_merit_badge_carousel_renders_overflow_pill_when_exceeding_max_visible(): void
    {
        $badges = Badge::take(4)->get();

        Livewire::test(MeritBadgeCarousel::class, [
            'badges' => $badges,
            'maxVisible' => 2,
        ])
            ->assertStatus(200)
            ->assertSee('+2')
            ->assertSee('More');
    }

    #[Test]
    public function test_merit_badge_carousel_renders_in_unboxed_mode_without_header(): void
    {
        $badge = Badge::first();

        Livewire::test(MeritBadgeCarousel::class, [
            'badges' => collect([$badge]),
            'boxed' => false,
            'showHeader' => false,
            'size' => 'lg',
        ])
            ->assertStatus(200)
            ->assertDontSee('View All')
            ->assertSee($badge->name)
            ->assertSee('+' . $badge->points . ' pts');
    }

    #[Test]
    public function test_merit_badge_carousel_renders_telemetry_inspector_strip(): void
    {
        $badge = Badge::first();

        Livewire::test(MeritBadgeCarousel::class, [
            'badges' => collect([$badge]),
            'showInspector' => true,
        ])
            ->assertStatus(200)
            ->assertSee($badge->name)
            ->assertSee('+' . $badge->points . ' pts');
    }

    #[Test]
    public function test_angler_profile_route_renders_merit_badge_carousel_in_hero(): void
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/angler/' . $angler->id);

        $response->assertStatus(200);
        $response->assertSeeLivewire(MeritBadgeCarousel::class);
    }

    #[Test]
    public function test_expedition_show_route_does_not_render_merit_badge_carousel(): void
    {
        $user = User::factory()->create();
        $expedition = Expedition::create([
            'description' => 'Lac des Mille Lacs Voyage',
            'start' => '2026-08-10',
            'finish' => '2026-08-15',
        ]);

        $response = $this->actingAs($user)->get('/expedition/' . $expedition->id);

        $response->assertStatus(200);
        $response->assertDontSeeLivewire(MeritBadgeCarousel::class);
    }

    #[Test]
    public function test_user_profile_route_renders_merit_badge_carousel_in_hero(): void
    {
        $user = User::factory()->create();
        Angler::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSeeLivewire(MeritBadgeCarousel::class);
    }

    #[Test]
    public function test_merit_badge_carousel_renders_modal_dialog_markup_and_teleport_container(): void
    {
        $badge = Badge::first();

        Livewire::test(MeritBadgeCarousel::class, [
            'badges' => collect([$badge]),
        ])
            ->assertStatus(200)
            ->assertSee('x-teleport="body"', false)
            ->assertSee('x-show="modalOpen"', false)
            ->assertSee('openModal', false)
            ->assertSee('closeModal', false)
            ->assertSee('prevModalBadge', false)
            ->assertSee('nextModalBadge', false)
            ->assertSee('Milestone Criteria / Trigger');
    }

    #[Test]
    public function test_merit_badge_carousel_extracts_catch_photo_and_telemetry_for_linked_record(): void
    {
        $user = User::factory()->create();
        $angler = Angler::factory()->create(['user_id' => $user->id]);
        $badge = Badge::first();
        $lake = Lake::factory()->create(['name' => 'Wabigoon Lake']);
        $fishBreed = FishBreed::factory()->create(['name' => 'Smallmouth Bass']);

        $record = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $fishBreed->id,
            'length' => 21.5,
            'weight' => 5.2,
            'caught' => now()->subDays(5),
        ]);

        $photo = Photo::create([
            'photoable_type' => Record::class,
            'photoable_id' => $record->id,
            'path' => 'photos/test-catch-pb.jpg',
            'caption' => 'Trophy Smallmouth Bass',
            'is_cover' => true,
        ]);

        AnglerBadge::create([
            'anglers_id' => $angler->id,
            'badge_id' => $badge->id,
            'record_id' => $record->id,
            'trigger_summary' => 'Caught 21.5" Smallmouth Bass at Wabigoon Lake',
            'awarded_at' => now()->subDays(5),
        ]);

        $test = Livewire::test(MeritBadgeCarousel::class, [
            'angler' => $angler,
        ]);

        $test->assertStatus(200);

        /** @var list<array<string, mixed>> $badges */
        $badges = $test->get('visibleBadges');
        $this->assertNotEmpty($badges);
        $firstDto = $badges[0];

        $this->assertEquals((string) $record->id, $firstDto['record_id']);
        $this->assertEquals('Smallmouth Bass', $firstDto['catch_species']);
        $this->assertEquals(21.5, $firstDto['catch_length']);
        $this->assertEquals('Wabigoon Lake', $firstDto['catch_lake']);
        $this->assertNotNull($firstDto['catch_photo_url']);

        // Assert rendered view contains the catch photo display logic and record link
        $test->assertSee('Qualifying Catch Photo')
            ->assertSee('View Associated Catch Record')
            ->assertSee('selectedModalBadge.record_id', false);
    }
}
