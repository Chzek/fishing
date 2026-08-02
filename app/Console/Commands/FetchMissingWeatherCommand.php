<?php

namespace Fishinglog\Console\Commands;

use Fishinglog\Models\Lake;
use Fishinglog\Models\Record;
use Fishinglog\Services\WeatherTelemetryService;
use Illuminate\Console\Command;

class FetchMissingWeatherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch missing weather telemetry for lakes and catch dates.';

    /**
     * Execute the console command.
     */
    public function handle(WeatherTelemetryService $weatherService)
    {
        $this->info('Checking catch records for missing daily weather telemetry...');

        $distinctCatches = Record::select('lakes_id', 'caught')
            ->distinct()
            ->get();

        if ($distinctCatches->isEmpty()) {
            $this->info('No catch records found.');
            return 0;
        }

        $count = 0;
        $bar = $this->output->createProgressBar($distinctCatches->count());
        $bar->start();

        foreach ($distinctCatches as $catch) {
            $lake = Lake::find($catch->lakes_id);

            if ($lake && !is_null($lake->latitude) && !is_null($lake->longitude)) {
                $weather = $weatherService->fetchForLakeAndDate($lake, $catch->caught);
                if ($weather && $weather->wasRecentlyCreated) {
                    $count++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully fetched weather telemetry for {$count} date(s).");

        return 0;
    }
}
