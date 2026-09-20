<?php

namespace Tests\Feature;

use Fishinglog\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminBackupsTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function guest_cannot_access_backups_console(): void
    {
        $response = $this->get('/admin/backups');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_cannot_access_backups_console(): void
    {
        $user = User::factory()->create(['type' => User::DEFAULT_TYPE]);

        $response = $this->actingAs($user)->get('/admin/backups');
        $response->assertRedirect('/home');
    }

    #[Test]
    public function admin_can_access_backups_console_with_telemetry(): void
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $response = $this->actingAs($admin)->get('/admin/backups');

        $response->assertStatus(200);
        $response->assertSee('NAS & Spatie Backup Console', false);
        $response->assertSee('Active Spatie Backup Configuration & Retention Policy', false);
        $response->assertSee('Backup Archives on Disk');
        $response->assertSee('Backup DB Now');
        $response->assertSee('Full Backup');
        $response->assertSee('Run Cleanup');
    }

    #[Test]
    public function admin_overview_page_displays_nas_backups_card(): void
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('NAS & Spatie Backups', false);
        $response->assertSee('Open Backups Console →', false);
        $response->assertSee('NAS Backups (', false);
    }

    #[Test]
    public function admin_can_trigger_database_backup(): void
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Artisan::spy();

        $response = $this->actingAs($admin)->post('/admin/backups/create', [
            'only_db' => true,
        ]);

        $response->assertRedirect('/admin/backups');
        $response->assertSessionHas('status', 'Database snapshot backup completed successfully!');

        Artisan::shouldHaveReceived('call')
            ->with('backup:run', ['--only-db' => true, '--disable-notifications' => true])
            ->once();
    }

    #[Test]
    public function admin_can_trigger_full_backup(): void
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Artisan::spy();

        $response = $this->actingAs($admin)->post('/admin/backups/create', [
            'only_db' => false,
        ]);

        $response->assertRedirect('/admin/backups');
        $response->assertSessionHas('status', 'Full application & database backup archive created successfully!');

        Artisan::shouldHaveReceived('call')
            ->with('backup:run', ['--disable-notifications' => true])
            ->once();
    }

    #[Test]
    public function admin_can_trigger_backup_cleanup(): void
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Artisan::spy();

        $response = $this->actingAs($admin)->post('/admin/backups/clean');

        $response->assertRedirect('/admin/backups');
        $response->assertSessionHas('status', 'Backup cleanup routine executed according to retention strategy!');

        Artisan::shouldHaveReceived('call')
            ->with('backup:clean', ['--disable-notifications' => true])
            ->once();
    }

    #[Test]
    public function admin_can_download_backup_archive(): void
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Storage::fake('backups');
        Storage::disk('backups')->put('Fishing-Logbook/fishing_backup_test.zip', 'dummy zip contents');

        $response = $this->actingAs($admin)->get('/admin/backups/download?disk=backups&file=Fishing-Logbook/fishing_backup_test.zip');

        $response->assertOk();
        $this->assertEquals('attachment; filename=fishing_backup_test.zip', $response->headers->get('content-disposition'));
    }

    #[Test]
    public function download_and_delete_rejects_path_traversal(): void
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        $responseDownload = $this->actingAs($admin)->get('/admin/backups/download?disk=backups&file=../../etc/passwd');
        $responseDownload->assertStatus(400);

        $responseDelete = $this->actingAs($admin)->delete('/admin/backups/delete', [
            'disk' => 'backups',
            'file' => '../../etc/passwd',
        ]);
        $responseDelete->assertStatus(400);
    }

    #[Test]
    public function admin_can_delete_backup_archive(): void
    {
        $admin = User::factory()->create(['type' => User::ADMIN_TYPE]);

        Storage::fake('backups');
        Storage::disk('backups')->put('Fishing-Logbook/to_delete.zip', 'content');

        $response = $this->actingAs($admin)->delete('/admin/backups/delete', [
            'disk' => 'backups',
            'file' => 'Fishing-Logbook/to_delete.zip',
        ]);

        $response->assertRedirect('/admin/backups');
        $response->assertSessionHas('status', 'Backup archive [to_delete.zip] has been deleted.');
        $this->assertFalse(Storage::disk('backups')->exists('Fishing-Logbook/to_delete.zip'));
    }
}
