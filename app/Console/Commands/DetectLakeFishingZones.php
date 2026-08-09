<?php

namespace Fishinglog\Console\Commands;

use Fishinglog\Models\Lake;
use Fishinglog\Services\GeoZoneDetector;
use Illuminate\Console\Command;

class DetectLakeFishingZones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lakes:sync-zones {--force : Force re-detection even if lake already has a fishing_zone_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically detect and assign Fisheries Management Zones (FMZs) for lakes based on GPS coordinates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        $query = Lake::whereNotNull('latitude')->whereNotNull('longitude');
        if (!$force) {
            $query->whereNull('fishing_zone_id');
        }

        $lakes = $query->get();
        if ($lakes->isEmpty()) {
            $this->info('No lakes requiring FMZ zone detection.');
            return 0;
        }

        $this->info("Processing FMZ point-in-polygon detection for {$lakes->count()} lakes...");

        $matchedCount = 0;
        $unmatchedCount = 0;

        foreach ($lakes as $lake) {
            $zone = GeoZoneDetector::detectZone($lake->latitude, $lake->longitude);

            if ($zone) {
                $lake->fishing_zone_id = $zone->id;
                $lake->save();
                $matchedCount++;
                $this->line("  <info>✓</info> Lake <comment>{$lake->name}</comment> ({$lake->latitude}, {$lake->longitude}) => <info>{$zone->code}</info> ({$zone->name})");
            } else {
                $unmatchedCount++;
                $this->line("  <fg=gray>-</fg=gray> Lake <comment>{$lake->name}</comment> ({$lake->latitude}, {$lake->longitude}) => No matching FMZ polygon found");
            }
        }

        $this->newLine();
        $this->info("FMZ Zone Sync Complete: {$matchedCount} lakes assigned to zones, {$unmatchedCount} unmatched.");

        return 0;
    }
}
