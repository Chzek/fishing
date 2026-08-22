<?php

namespace Tests\Feature;

use Fishinglog\Models\Angler;
use Fishinglog\Models\FishBreed;
use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BladeComponentsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function status_badge_component_renders_correctly()
    {
        $view = $this->blade('<x-statusBadge type="synced" />');
        $view->assertSee('Synced');

        $viewPending = $this->blade('<x-statusBadge type="pending" />');
        $viewPending->assertSee('Pending Sync');

        $viewPb = $this->blade('<x-statusBadge type="pb" />');
        $viewPb->assertSee('Personal Best');
    }

    #[Test]
    public function empty_state_component_renders_correctly()
    {
        $view = $this->blade('<x-emptyState icon="fish-off" title="No Catches Yet" description="Log your first catch." />');
        $view->assertSee('No Catches Yet');
        $view->assertSee('Log your first catch.');
    }

    #[Test]
    public function card_component_renders_correctly()
    {
        $view = $this->blade('<x-card title="Telemetry Overview" icon="anchor"><span>Card Content</span></x-card>');
        $view->assertSee('Telemetry Overview');
        $view->assertSee('Card Content');
    }

    #[Test]
    public function catch_card_component_renders_correctly()
    {
        $angler = Angler::factory()->create();
        $breed = FishBreed::factory()->create(['name' => 'Crappie']);
        $lake = Lake::factory()->create(['name' => 'Black Lake']);

        $record = Record::create([
            'anglers_id' => $angler->id,
            'fish_breeds_id' => $breed->id,
            'lakes_id' => $lake->id,
            'length' => 14.5,
            'caught' => now(),
        ]);

        $view = $this->blade('<x-catchCard :record="$record" />', ['record' => $record]);
        $view->assertSee('Crappie');
        $view->assertSee('14.5');
        $view->assertSee('Black Lake');
    }

    #[Test]
    public function kpi_metric_component_renders_correctly()
    {
        $view = $this->blade('<x-kpiMetric label="Unique Waters" value="18" icon="waves" color="teal" subtext="Visited Lakes" />');
        $view->assertSee('Unique Waters');
        $view->assertSee('18');
        $view->assertSee('Visited Lakes');
    }

    #[Test]
    public function lake_card_component_renders_correctly()
    {
        $lake = Lake::factory()->create([
            'name' => 'Mirror Lake',
            'county' => 'Essex',
            'state' => 'NY',
            'latitude' => 44.28,
            'longitude' => -73.98,
        ]);

        $view = $this->blade('<x-lakeCard :lake="$lake" :catchesCount="12" />', ['lake' => $lake]);
        $view->assertSee('Mirror Lake');
        $view->assertSee('Essex, NY');
        $view->assertSee('12 catches');
    }
}


