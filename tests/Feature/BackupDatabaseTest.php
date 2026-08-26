<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BackupDatabaseTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_runs_database_backup_artisan_command()
    {
        $this->artisan('db:backup --gzip')
            ->assertExitCode(0);

        $files = glob(database_path('backups/fishing_backup_*.sql.gz'));
        $this->assertNotEmpty($files);
    }
}
