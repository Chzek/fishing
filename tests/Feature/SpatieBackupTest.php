<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SpatieBackupTest extends TestCase
{
    /**
     * Test that Spatie backup configuration is properly loaded with retention rules.
     */
    public function test_backup_configuration_has_expected_retention_and_sources(): void
    {
        $this->assertNotNull(config('backup.backup'));
        $this->assertContains('mysql', config('backup.backup.source.databases'));
        $this->assertContains('backups', config('backup.backup.destination.disks'));

        // Validate multi-tier retention strategy configuration
        $cleanupStrategy = config('backup.cleanup.default_strategy');
        $this->assertEquals(7, $cleanupStrategy['keep_all_backups_for_days']);
        $this->assertEquals(30, $cleanupStrategy['keep_daily_backups_for_days']);
        $this->assertEquals(8, $cleanupStrategy['keep_weekly_backups_for_weeks']);
        $this->assertEquals(12, $cleanupStrategy['keep_monthly_backups_for_months']);
        $this->assertEquals(2, $cleanupStrategy['keep_yearly_backups_for_years']);
        $this->assertEquals(5000, $cleanupStrategy['delete_oldest_backups_when_using_more_megabytes_than']);
    }

    /**
     * Test that backup and cleanup artisan commands are registered and callable.
     */
    public function test_backup_commands_are_registered_in_artisan(): void
    {
        $allCommands = Artisan::all();

        $this->assertArrayHasKey('backup:run', $allCommands);
        $this->assertArrayHasKey('backup:clean', $allCommands);
        $this->assertArrayHasKey('backup:list', $allCommands);
        $this->assertArrayHasKey('backup:monitor', $allCommands);
    }
}
