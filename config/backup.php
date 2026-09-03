<?php

return [

    'backup' => [

        /*
         * The name of this application. You can use this name to monitor
         * the backups.
         */
        'name' => env('APP_NAME', 'Fishing-Logbook'),

        'source' => [

            'files' => [

                /*
                 * The list of files and directories that should be included in the backup.
                 */
                'include' => [
                    storage_path('app/public'),
                ],

                /*
                 * These paths will be excluded from the backup.
                 */
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    base_path('.git'),
                    base_path('tests'),
                    storage_path('app/backups'),
                    storage_path('app/backup-temp'),
                ],

                /*
                 * Determines if symlinks should be followed.
                 */
                'follow_links' => false,

                /*
                 * Determines if it should avoid unreadable folders.
                 */
                'ignore_unreadable_directories' => false,

                /*
                 * This subdirectory is used when creating the backup.
                 */
                'relative_path' => null,
            ],

            /*
             * The names of the connections to the databases that should be backed up
             * MySQL, PostgreSQL, SQLite and Mongo databases are supported.
             */
            'databases' => [
                'mysql',
            ],
        ],

        /*
         * The database dump can be customized with extra options.
         */
        'database_dump_customizer' => null,

        /*
         * The database dump can be modified (for example, to sanitize data).
         */
        'database_dump_transformer' => null,

        'destination' => [

            /*
             * The compression algorithm to be used for creating the archive.
             */
            'compression_method' => ZipArchive::CM_DEFAULT,

            /*
             * The compression level for the backup archive.
             */
            'compression_level' => 9,

            /*
             * The filename prefix to be used for the backup archive.
             */
            'filename_prefix' => 'fishing_backup_',

            /*
             * The disk names on which the backups will be stored.
             */
            'disks' => [
                'backups',
            ],
        ],

        /*
         * The directory where the temporary files will be stored.
         */
        'temporary_directory' => storage_path('app/backup-temp'),

        /*
         * The password to be used for encrypting the backup.
         */
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        /*
         * The encryption algorithm to be used for encrypting the backup.
         */
        'encryption' => 'default',

        /*
         * The max execution time for the backup process in seconds.
         */
        'tries' => 1,

        'retry_delay' => 0,
    ],

    /*
     * You can get notified when specific events occur.
     */
    'notifications' => [

        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['log'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['log'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['log'],
            \Spatie\Backup\Notifications\Notifications\BackupSuccessfulNotification::class => ['log'],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => ['log'],
            \Spatie\Backup\Notifications\Notifications\CleanupSuccessfulNotification::class => ['log'],
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent.
         */
        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => 'angler@example.com',
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Fishing Logbook'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',
            'channel' => null,
            'username' => null,
            'icon' => null,
        ],

        'discord' => [
            'webhook_url' => '',
            'username' => null,
            'avatar_url' => null,
        ],
    ],

    /*
     * Here you can specify which backups should be monitored.
     */
    'monitor_backups' => [
        [
            'name' => env('APP_NAME', 'Fishing-Logbook'),
            'disks' => ['backups'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        /*
         * The strategy that will be used to cleanup old backups.
         */
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [

            /*
             * The number of days for which backups must be kept.
             */
            'keep_all_backups_for_days' => 7,

            /*
             * The number of days for which daily backups must be kept.
             */
            'keep_daily_backups_for_days' => 30,

            /*
             * The number of weeks for which one weekly backup must be kept.
             */
            'keep_weekly_backups_for_weeks' => 8,

            /*
             * The number of months for which one monthly backup must be kept.
             */
            'keep_monthly_backups_for_months' => 12,

            /*
             * The number of years for which one yearly backup must be kept.
             */
            'keep_yearly_backups_for_years' => 2,

            /*
             * After cleaning up the backups, remove the oldest backup until
             * this amount of megabytes has been reached.
             */
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],

        'tries' => 1,

        'retry_delay' => 0,
    ],

];
