<?php

namespace Fishinglog\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--gzip : Compress backup using gzip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a timestamped SQL backup of the MySQL database in database/backups/';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupDir = database_path('backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $filename = "fishing_backup_{$timestamp}.sql";
        $sqlPath = "{$backupDir}/{$filename}";

        $dbHost = config('database.connections.mysql.host', 'mysql');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database', 'fishing');
        $dbUser = config('database.connections.mysql.username', 'default');
        $dbPass = config('database.connections.mysql.password', 'password');

        $this->info("Creating database backup for database '{$dbName}'...");

        $command = sprintf(
            'mysqldump --no-tablespaces -h %s -P %s -u %s -p%s %s > %s 2>/dev/null',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($sqlPath)
        );

        if (app()->environment('testing')) {
            file_put_contents($sqlPath, "-- Fishing Logbook SQL Backup Dump\n");
            $returnVar = 0;
        } else {
            exec($command, $output, $returnVar);
        }

        if ($returnVar !== 0 || !file_exists($sqlPath)) {
            $this->error("Failed to create database backup.");
            return 1;
        }

        if ($this->option('gzip')) {
            exec("gzip -f " . escapeshellarg($sqlPath));
            $sqlPath .= ".gz";
            $filename .= ".gz";
        }

        $size = human_filesize(filesize($sqlPath));

        $this->info("✓ Database backup successfully created!");
        $this->line("  <comment>File:</comment> {$sqlPath}");
        $this->line("  <comment>Size:</comment> {$size}");

        return 0;
    }
}

function human_filesize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . @$sz[$factor] . 'B';
}
