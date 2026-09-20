<?php

namespace Fishinglog\Http\Controllers\Admin;

use Fishinglog\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\Config\Config as SpatieBackupConfig;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class AdminBackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $config = SpatieBackupConfig::fromArray(config('backup'));
        $statuses = BackupDestinationStatusFactory::createForMonitorConfig($config->monitoredBackups);

        $destinationsData = [];
        $totalBackupCount = 0;
        $totalStorageBytes = 0;
        $allBackups = [];

        foreach ($statuses as $status) {
            $dest = $status->backupDestination();
            $isHealthy = $status->isHealthy();
            $healthCheckFailure = $status->getHealthCheckFailure();
            $failureMessage = $healthCheckFailure ? $healthCheckFailure->exception()->getMessage() : null;

            $backupCollection = $dest->backups();
            $count = $backupCollection->count();
            $usedStorage = $dest->usedStorage();
            $totalBackupCount += $count;
            $totalStorageBytes += $usedStorage;

            $backupsList = [];
            foreach ($backupCollection as $backup) {
                $backupsList[] = [
                    'path' => $backup->path(),
                    'filename' => basename($backup->path()),
                    'size_bytes' => $backup->sizeInBytes(),
                    'size_formatted' => $this->formatBytes($backup->sizeInBytes()),
                    'date' => $backup->date(),
                    'date_formatted' => $backup->date()->format('Y-m-d H:i:s'),
                    'age_human' => $backup->date()->diffForHumans(),
                    'exists' => $backup->exists(),
                    'disk' => $dest->diskName(),
                ];
            }

            // Sort backups descending (newest first)
            usort($backupsList, fn ($a, $b) => $b['date']->timestamp <=> $a['date']->timestamp);

            $destinationsData[] = [
                'name' => $dest->backupName(),
                'disk' => $dest->diskName(),
                'is_reachable' => $dest->isReachable(),
                'is_healthy' => $isHealthy,
                'failure_message' => $failureMessage,
                'count' => $count,
                'used_storage_bytes' => $usedStorage,
                'used_storage_formatted' => $this->formatBytes($usedStorage),
                'newest_backup' => $dest->newestBackup() ? $dest->newestBackup()->date() : null,
                'oldest_backup' => $dest->oldestBackup() ? $dest->oldestBackup()->date() : null,
                'backups' => $backupsList,
            ];

            foreach ($backupsList as $b) {
                $allBackups[] = $b;
            }
        }

        usort($allBackups, fn ($a, $b) => $b['date']->timestamp <=> $a['date']->timestamp);

        $spatieConfig = [
            'app_name' => config('backup.backup.name', 'Fishing Logbook'),
            'disks' => config('backup.backup.destination.disks', ['backups']),
            'filename_prefix' => config('backup.backup.destination.filename_prefix', 'fishing_backup_'),
            'databases' => config('backup.backup.source.databases', ['mysql']),
            'include_files' => config('backup.backup.source.files.include', []),
            'exclude_files' => config('backup.backup.source.files.exclude', []),
            'compression_level' => config('backup.backup.destination.compression_level', 9),
            'keep_all_days' => config('backup.cleanup.default_strategy.keep_all_backups_for_days', 7),
            'keep_daily_days' => config('backup.cleanup.default_strategy.keep_daily_backups_for_days', 30),
            'keep_weekly_weeks' => config('backup.cleanup.default_strategy.keep_weekly_backups_for_weeks', 8),
            'keep_monthly_months' => config('backup.cleanup.default_strategy.keep_monthly_backups_for_months', 12),
            'max_megabytes' => config('backup.cleanup.default_strategy.delete_oldest_backups_when_using_more_megabytes_than', 5000),
        ];

        return view('admin.backups.index', [
            'destinations' => $destinationsData,
            'allBackups' => $allBackups,
            'totalBackupCount' => $totalBackupCount,
            'totalStorageBytes' => $totalStorageBytes,
            'totalStorageFormatted' => $this->formatBytes($totalStorageBytes),
            'spatieConfig' => $spatieConfig,
        ]);
    }

    public function create(Request $request)
    {
        $onlyDb = $request->boolean('only_db', true);

        try {
            if ($onlyDb) {
                Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => true]);
                $message = 'Database snapshot backup completed successfully!';
            } else {
                Artisan::call('backup:run', ['--disable-notifications' => true]);
                $message = 'Full application & database backup archive created successfully!';
            }

            return redirect()->route('admin.backups')->with('status', $message);
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups')->with('error', "Backup execution failed: {$e->getMessage()}");
        }
    }

    public function clean()
    {
        try {
            Artisan::call('backup:clean', ['--disable-notifications' => true]);
            return redirect()->route('admin.backups')->with('status', 'Backup cleanup routine executed according to retention strategy!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups')->with('error', "Backup cleanup failed: {$e->getMessage()}");
        }
    }

    public function download(Request $request)
    {
        $request->validate([
            'disk' => 'required|string',
            'file' => 'required|string',
        ]);

        $diskName = $request->string('disk')->value();
        $filePath = $request->string('file')->value();

        // Prevent path traversal
        if (str_contains($filePath, '..')) {
            abort(400, 'Invalid file path.');
        }

        $disk = Storage::disk($diskName);

        if (!$disk->exists($filePath)) {
            return redirect()->route('admin.backups')->with('error', "Backup archive [{$filePath}] not found on disk [{$diskName}].");
        }

        return $disk->download($filePath, basename($filePath));
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'disk' => 'required|string',
            'file' => 'required|string',
        ]);

        $diskName = $request->string('disk')->value();
        $filePath = $request->string('file')->value();

        if (str_contains($filePath, '..')) {
            abort(400, 'Invalid file path.');
        }

        $disk = Storage::disk($diskName);

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
            return redirect()->route('admin.backups')->with('status', "Backup archive [".basename($filePath)."] has been deleted.");
        }

        return redirect()->route('admin.backups')->with('error', "Backup archive not found.");
    }

    protected function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = (int) min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
