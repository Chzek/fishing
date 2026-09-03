<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console Schedule
|--------------------------------------------------------------------------
|
| Automated backups and maintenance are scheduled to execute in Production
| (Synology NAS). Local development remains unburdened by background crons.
|
*/
if (app()->isProduction()) {
    \Illuminate\Support\Facades\Schedule::command('backup:clean')->dailyAt('01:00');
    \Illuminate\Support\Facades\Schedule::command('backup:run --only-db')->dailyAt('02:00');
    \Illuminate\Support\Facades\Schedule::command('backup:run')->weeklyOn(0, '03:00');
}

