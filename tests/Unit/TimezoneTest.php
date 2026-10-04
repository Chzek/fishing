<?php

namespace Tests\Unit;

use Fishinglog\Livewire\Modals\QuickCatchModal;
use Fishinglog\Models\Angler;
use Fishinglog\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TimezoneTest extends TestCase
{
    #[Test]
    public function application_default_timezone_is_configured_to_america_detroit()
    {
        $this->assertEquals('America/Detroit', config('app.timezone'));
        $this->assertEquals('America/Detroit', date_default_timezone_get());
    }

    #[Test]
    public function quick_catch_modal_mount_defaults_to_local_detroit_date_in_evening_hours()
    {
        // 11:30 PM EDT (03:30 AM UTC next day)
        Carbon::setTestNow(Carbon::parse('2026-08-15 23:30:00', 'America/Detroit'));

        $this->assertEquals('2026-08-15', now()->format('Y-m-d'));

        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(QuickCatchModal::class);
        $this->assertEquals('2026-08-15', $component->get('caught'));

        Carbon::setTestNow();
    }
}
