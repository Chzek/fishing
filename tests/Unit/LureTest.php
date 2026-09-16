<?php

namespace Tests\Unit;

use Fishinglog\Models\Lure;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LureTest extends TestCase
{
    #[Test]
    public function it_resolves_distinct_swatch_gradients_for_fishing_colorways(): void
    {
        $twilight = Lure::resolveSwatchGradient('Twilight (Purple/Pink)');
        $perch = Lure::resolveSwatchGradient('Pro Yellow Perch');
        $craw = Lure::resolveSwatchGradient('California Craw');
        $mud = Lure::resolveSwatchGradient('Deal / Mud Minnow');
        $coppertreuse = Lure::resolveSwatchGradient('Coppertreuse');

        // Verify all 5 gradients from the screenshot are distinct and non-empty
        $gradients = [$twilight, $perch, $craw, $mud, $coppertreuse];
        $this->assertCount(5, array_unique($gradients), 'All 5 lure colorways must have distinct gradients');

        // Verify specific colorway mappings
        $this->assertStringContainsString('purple', $twilight);
        $this->assertStringContainsString('amber', $perch);
        $this->assertStringContainsString('rose', $craw);
        $this->assertStringContainsString('stone', $mud);
        $this->assertStringContainsString('lime', $coppertreuse);
    }

    #[Test]
    public function it_resolves_swatch_gradient_via_model_accessor(): void
    {
        $lure = new Lure([
            'name' => 'Finesse TRD',
            'brand' => 'Z-Man',
            'color' => 'Coppertreuse',
        ]);

        $this->assertStringContainsString('lime', $lure->swatch_gradient);
    }

    #[Test]
    public function it_formats_display_name_cleanly(): void
    {
        $lure = new Lure([
            'name' => 'Shad Rap',
            'brand' => 'Rapala',
            'color' => 'Firetiger',
            'size' => '2"',
            'depth_range' => '4-9 ft',
        ]);

        $this->assertEquals('Rapala Shad Rap (Firetiger • 2" • 4-9 ft)', $lure->display_name);
    }
}
