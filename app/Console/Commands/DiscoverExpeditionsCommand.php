<?php

namespace Fishinglog\Console\Commands;

use Fishinglog\Services\ExpeditionDiscoveryService;
use Illuminate\Console\Command;

class DiscoverExpeditionsCommand extends Command
{
    protected $signature = 'journal:discover-expeditions {--auto-create : Automatically create recommended expeditions}';

    protected $description = 'Analyze journal entry dates and recommend missing Expeditions.';

    public function handle(ExpeditionDiscoveryService $discoveryService): int
    {
        $this->info('Analyzing journal entries for missing Expedition trips...');

        $recommendations = $discoveryService->getRecommendedExpeditions();

        if ($recommendations->isEmpty()) {
            $this->info('No unassigned journal entries or missing expeditions discovered.');
            return 0;
        }

        $this->info("Found {$recommendations->count()} recommended Expedition trip(s):");

        $autoCreate = (bool) $this->option('auto-create');

        foreach ($recommendations as $index => $rec) {
            $num = $index + 1;
            $anglersList = $rec['matched_anglers']->pluck('full_name')->implode(', ') ?: 'None detected';
            $lakesList = $rec['matched_lakes']->pluck('name')->implode(', ') ?: 'None detected';

            $this->line("--------------------------------------------------");
            $this->info("Trip #{$num}: {$rec['suggested_title']}");
            $this->line("  Window:   {$rec['start_date']} to {$rec['finish_date']} ({$rec['days_count']} days)");
            $this->line("  Entries:  {$rec['entries_count']} journal entry log(s)");
            $this->line("  Anglers:  {$anglersList}");
            $this->line("  Lakes:    {$lakesList}");

            if ($autoCreate) {
                $expedition = $discoveryService->createFromRecommendation($rec);
                $this->info("  -> Created Expedition [{$expedition->id}]: {$expedition->title}");
            }
        }

        $this->line("--------------------------------------------------");

        if (!$autoCreate) {
            $this->comment('Tip: Run `php artisan journal:discover-expeditions --auto-create` to automatically create these Expeditions in the database.');
        }

        return 0;
    }
}
