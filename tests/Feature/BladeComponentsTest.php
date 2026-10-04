<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BladeComponentsTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function status_alert_component_renders_correctly()
    {
        $view = $this->blade('<x-statusAlert status="Telemetry record updated successfully." />');
        $view->assertSee('Telemetry record updated successfully.');
        $view->assertSee('<svg', false);

        $viewError = $this->blade('<x-statusAlert status="Unable to connect to database." type="error" />');
        $viewError->assertSee('Unable to connect to database.');
        $viewError->assertSee('bg-rose-50', false);
    }

    #[Test]
    public function empty_state_component_renders_correctly()
    {
        $view = $this->blade('<x-emptyState icon="fish-off" title="No Catches Yet" description="Log your first catch." />');
        $view->assertSee('No Catches Yet');
        $view->assertSee('Log your first catch.');
        $view->assertSee('<svg', false);
    }

    #[Test]
    public function kpi_metric_component_renders_correctly()
    {
        $view = $this->blade('<x-kpiMetric label="Unique Waters" value="18" icon="waves" color="teal" subtext="Visited Lakes" />');
        $view->assertSee('Unique Waters');
        $view->assertSee('18');
        $view->assertSee('Visited Lakes');
        $view->assertSee('<svg', false);
    }

    #[Test]
    public function fish_avatar_fallback_renders_svg_icon()
    {
        $view = $this->blade('<x-fishAvatar />');
        $view->assertSee('<svg', false);
    }

    #[Test]
    public function card_component_renders_correctly_with_props_and_slots()
    {
        $view = $this->blade('<x-card title="Telemetry Card" subtitle="Card Subtitle" icon="lucide-activity" badge="Active" badgeVariant="emerald">
            <p>Body Content</p>
            <x-slot:actions>
                <button type="button">Action Button</button>
            </x-slot:actions>
            <x-slot:footer>
                <div>Footer Telemetry</div>
            </x-slot:footer>
        </x-card>');

        $view->assertSee('Telemetry Card');
        $view->assertSee('Card Subtitle');
        $view->assertSee('Active');
        $view->assertSee('Body Content');
        $view->assertSee('Action Button');
        $view->assertSee('Footer Telemetry');
        $view->assertSee('<svg', false);
    }

    #[Test]
    public function badge_component_renders_variants_and_sizes()
    {
        $tealBadge = $this->blade('<x-badge variant="teal" size="sm" label="14.5 in" :fontMono="true" icon="lucide-tag" />');
        $tealBadge->assertSee('14.5 in');
        $tealBadge->assertSee('font-mono');
        $tealBadge->assertSee('bg-teal-50');
        $tealBadge->assertSee('<svg', false);

        $amberBadge = $this->blade('<x-badge variant="amber" size="lg">Trophy Master</x-badge>');
        $amberBadge->assertSee('Trophy Master');
        $amberBadge->assertSee('bg-amber-50');
    }

    #[Test]
    public function page_hero_component_renders_correctly()
    {
        $view = $this->blade('<x-pageHero title="Lakes & Waters" subtitle="Directory of Canadian Waters" icon="lucide-waves" badge="18 Lakes" badgeVariant="teal">
            <x-slot:actions>
                <a href="/lake/create">Add Lake</a>
            </x-slot:actions>
            <x-slot:metrics>
                <div>Total: 1,420 Catches</div>
            </x-slot:metrics>
        </x-pageHero>');

        $view->assertSee('Lakes & Waters');
        $view->assertSee('Directory of Canadian Waters');
        $view->assertSee('18 Lakes');
        $view->assertSee('Add Lake');
        $view->assertSee('Total: 1,420 Catches');
        $view->assertSee('<svg', false);
    }

    #[Test]
    public function watermark_components_render_correctly()
    {
        $tape = $this->blade('<x-watermarkTapeMeasure />');
        $tape->assertSee('<svg', false);
        $tape->assertSee('viewBox="0 0 400 180"', false);

        $dial = $this->blade('<x-watermarkDialScale />');
        $dial->assertSee('<svg', false);
        $dial->assertSee('viewBox="0 0 140 140"', false);

        $topRod = $this->blade('<x-watermarkTopRod />');
        $topRod->assertSee('<svg', false);
        $topRod->assertSee('viewBox="0 0 400 180"', false);

        $can = $this->blade('<x-watermarkPouringCan />');
        $can->assertSee('<svg', false);
        $can->assertSee('BLUE');

        $lure = $this->blade('<x-watermarkLure category="crankbait" />');
        $lure->assertSee('<svg', false);
        $lure->assertSee('viewBox="0 0 400 180"', false);
    }

    #[Test]
    public function specimen_card_component_renders_correctly()
    {
        $angler = Angler::factory()->create(['firstName' => 'Lucas', 'middleName' => '', 'lastName' => 'Hale']);
        $lake = Lake::factory()->create(['name' => 'Rainy Lake']);
        $breed = FishBreed::factory()->create(['name' => 'Smallmouth Bass']);

        $record = Record::factory()->create([
            'anglers_id' => $angler->id,
            'lakes_id' => $lake->id,
            'fish_breeds_id' => $breed->id,
            'length' => 20.75,
            'weight' => 5.30,
            'caught' => '2026-06-25',
        ]);

        // Default with showLake (e.g. on /fish/{id})
        $fishView = $this->blade('<x-specimenCard :rank="0" :record="$record" :showLake="true" />', ['record' => $record]);
        $fishView->assertSee('#1');
        $fishView->assertSee('20.8"', false);
        $fishView->assertSee('5.30 lbs');
        $fishView->assertSee('Lucas Hale');
        $fishView->assertSee('Rainy Lake');
        $fishView->assertSee('Jun 2026');

        // With showSpecies (e.g. on /lake/{id})
        $lakeView = $this->blade('<x-specimenCard :rank="1" :record="$record" :showSpecies="true" />', ['record' => $record]);
        $lakeView->assertSee('#2');
        $lakeView->assertSee('Smallmouth Bass');

        // With both showLake and showSpecies (e.g. on /expedition/{id})
        $expeditionView = $this->blade('<x-specimenCard :rank="2" :record="$record" :showLake="true" :showSpecies="true" />', ['record' => $record]);
        $expeditionView->assertSee('#3');
        $expeditionView->assertSee('Smallmouth Bass • Rainy Lake');

        // With custom subtitle override
        $customView = $this->blade('<x-specimenCard :rank="0" :record="$record" subtitle="Special Expedition Trophy" />', ['record' => $record]);
        $customView->assertSee('Special Expedition Trophy');
    }
}



