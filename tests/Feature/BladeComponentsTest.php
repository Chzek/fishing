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
}



